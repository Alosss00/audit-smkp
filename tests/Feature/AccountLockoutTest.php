<?php

namespace Tests\Feature;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountLockoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Account is locked for 15 minutes after 5 consecutive failed login attempts.
     */
    public function test_account_is_locked_after_five_failed_attempts(): void
    {
        $user = User::factory()->create([
            'username'              => 'testuser_lockout',
            'password'              => 'CorrectPassword123!',
            'is_active'             => true,
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);

        // 4 failed attempts from different IPs (distributed brute force simulation)
        for ($i = 1; $i <= 4; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.{$i}"])
                ->from(route('login'))
                ->post(route('login.post'), [
                    'username' => 'testuser_lockout',
                    'password' => 'WrongPassword',
                ]);

            $response->assertSessionHasErrors('username');
            $this->assertFalse($user->fresh()->isLocked());
            $this->assertSame($i, $user->fresh()->failed_login_attempts);
        }

        // 5th failed attempt triggers account lockout
        $response5 = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
            ->from(route('login'))
            ->post(route('login.post'), [
                'username' => 'testuser_lockout',
                'password' => 'WrongPassword',
            ]);

        $response5->assertSessionHasErrors('username');

        $user->refresh();
        $this->assertTrue($user->isLocked());
        $this->assertSame(5, $user->failed_login_attempts);
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->isFuture());

        // Check LoginLog has 'locked' status
        $this->assertDatabaseHas('login_logs', [
            'username' => 'testuser_lockout',
            'status'   => 'locked',
        ]);
    }

    /**
     * 6th login attempt is rejected even with the correct password when account is locked.
     */
    public function test_locked_account_cannot_login_even_with_correct_password(): void
    {
        $user = User::factory()->create([
            'username'              => 'locked_user',
            'password'              => 'CorrectPassword123!',
            'is_active'             => true,
            'failed_login_attempts' => 5,
            'locked_until'          => now()->addMinutes(12),
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.6'])
            ->from(route('login'))
            ->post(route('login.post'), [
                'username' => 'locked_user',
                'password' => 'CorrectPassword123!',
            ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();

        // Error message mentions lock status
        $errors = session('errors')->get('username');
        $this->assertStringContainsString('terkunci', $errors[0]);

        // Attempt logged as 'locked'
        $this->assertDatabaseHas('login_logs', [
            'user_id'  => $user->id,
            'username' => 'locked_user',
            'status'   => 'locked',
        ]);
    }

    /**
     * Administrator can unlock a locked user account via the management endpoint.
     */
    public function test_admin_can_unlock_account(): void
    {
        $admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $targetUser = User::factory()->create([
            'username'              => 'user_to_unlock',
            'password'              => 'MySecurePass123!',
            'role'                  => 'auditor',
            'area'                  => 'Site Alpha',
            'is_active'             => true,
            'failed_login_attempts' => 5,
            'locked_until'          => now()->addMinutes(14),
        ]);

        $this->assertTrue($targetUser->isLocked());

        // Admin unlocks user
        $response = $this->actingAs($admin)
            ->post(route('admin.users.unlock', $targetUser->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertFalse($targetUser->isLocked());
        $this->assertSame(0, $targetUser->failed_login_attempts);
        $this->assertNull($targetUser->locked_until);

        // Target user can now login with correct password
        auth()->logout();
        $this->flushSession();
        $loginResponse = $this->post(route('login.post'), [
            'username' => 'user_to_unlock',
            'password' => 'MySecurePass123!',
        ]);

        $loginResponse->assertRedirect();
        $this->assertAuthenticatedAs($targetUser);
    }

    /**
     * Successful authentication resets failed_login_attempts and locked_until counters.
     */
    public function test_successful_login_resets_failed_attempts_counter(): void
    {
        $user = User::factory()->create([
            'username'              => 'user_reset_counter',
            'password'              => 'ValidPassword999!',
            'role'                  => 'auditor',
            'area'                  => 'Site Alpha',
            'is_active'             => true,
            'failed_login_attempts' => 3,
            'locked_until'          => null,
        ]);

        $response = $this->from(route('login'))
            ->post(route('login.post'), [
                'username' => 'user_reset_counter',
                'password' => 'ValidPassword999!',
            ]);

        $response->assertRedirect(route('auditor.dashboard'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertSame(0, $user->failed_login_attempts);
        $this->assertNull($user->locked_until);
    }
}
