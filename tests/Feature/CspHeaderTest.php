<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CspHeaderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Public page (/login) contains a valid CSP header with required security directives.
     */
    public function test_public_page_contains_csp_header(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Security-Policy');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);

        // unsafe-eval and unsafe-inline should NOT be in script-src
        $this->assertStringNotContainsString("'unsafe-eval'", $csp);

        preg_match("/script-src\s+([^;]+)/", $csp, $matches);
        $this->assertNotEmpty($matches, 'script-src directive not found in CSP header');
        $scriptSrc = $matches[1];
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc);
    }

    /**
     * Nonce in CSP header matches nonce attribute in rendered script tags.
     */
    public function test_csp_nonce_matches_script_tags(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $csp = $response->headers->get('Content-Security-Policy');

        $matched = preg_match("/'nonce-([A-Za-z0-9+\/=]+)'/", $csp, $matches);
        $this->assertSame(1, $matched, 'Nonce not found in CSP header');

        $nonce = $matches[1];
        $this->assertNotEmpty($nonce);

        // Verify body HTML uses the same nonce
        $response->assertSee('nonce="' . $nonce . '"', false);
    }

    /**
     * Authenticated pages also carry CSP header and synchronize nonce.
     */
    public function test_authenticated_page_contains_csp_header(): void
    {
        $admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Security-Policy');

        $csp = $response->headers->get('Content-Security-Policy');
        preg_match("/'nonce-([A-Za-z0-9+\/=]+)'/", $csp, $matches);
        $this->assertNotEmpty($matches, 'Nonce not found in CSP header');

        $nonce = $matches[1];
        $response->assertSee('nonce="' . $nonce . '"', false);
    }

    /**
     * Consecutive requests produce different cryptographically random nonces.
     */
    public function test_consecutive_requests_produce_different_nonces(): void
    {
        $response1 = $this->get(route('login'));
        $csp1 = $response1->headers->get('Content-Security-Policy');
        preg_match("/'nonce-([A-Za-z0-9+\/=]+)'/", $csp1, $matches1);
        $nonce1 = $matches1[1] ?? '';

        $response2 = $this->get(route('login'));
        $csp2 = $response2->headers->get('Content-Security-Policy');
        preg_match("/'nonce-([A-Za-z0-9+\/=]+)'/", $csp2, $matches2);
        $nonce2 = $matches2[1] ?? '';

        $this->assertNotEmpty($nonce1);
        $this->assertNotEmpty($nonce2);
        $this->assertNotEquals($nonce1, $nonce2);
    }

    /**
     * Login logs view does not contain inline onchange event handler.
     */
    public function test_login_logs_view_has_no_inline_onchange_handler(): void
    {
        $admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.login-logs.index'));

        $response->assertStatus(200);
        $response->assertDontSee('onchange="toggleCustomDate', false);
        $response->assertSee('id="periodSelect"', false);
        $response->assertSee("periodSelect.addEventListener('change'", false);
    }
}
