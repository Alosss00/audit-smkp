<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            'role'     => 'required|in:admin,auditor',
            'area'     => 'required_if:role,auditor|nullable|string|max:255',
            'password' => 'required|string|min:6',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'password.min'    => 'Password minimal 6 karakter.',
            'area.required_if' => 'Area kerja wajib diisi untuk pengguna dengan role Auditor (Auditee / PIC Area).',
        ]);

        $newUser = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'role'      => $request->role,
            'area'      => $request->area,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Manajemen User',
            'tindakan'        => "Membuat akun user baru: {$newUser->name} ({$newUser->username})",
            'data_lama'       => null,
            'data_baru'       => ['name' => $newUser->name, 'username' => $newUser->username, 'role' => $newUser->role, 'area' => $newUser->area],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'nullable|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,auditor',
            'area'     => 'required_if:role,auditor|nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ], [
            'area.required_if' => 'Area kerja wajib diisi untuk pengguna dengan role Auditor (Auditee / PIC Area).',
        ]);

        $originalData = ['name' => $user->name, 'username' => $user->username, 'email' => $user->email, 'role' => $user->role, 'area' => $user->area, 'is_active' => $user->is_active];
        $isActive = $request->has('is_active') ? true : false;

        $data = [
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'role'      => $request->role,
            'area'      => $request->area,
            'is_active' => $isActive,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Manajemen User',
            'tindakan'        => "Mengubah data user: {$user->name} ({$user->username})",
            'data_lama'       => $originalData,
            'data_baru'       => ['name' => $user->name, 'username' => $user->username, 'email' => $user->email, 'role' => $user->role, 'area' => $user->area, 'is_active' => $user->is_active],
            'waktu_perubahan' => now(),
        ]);

        // Force logout active session if deactivated
        if (!$user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Toggle active/inactive status of a user.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status aktif akun Anda sendiri.');
        }

        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Manajemen User',
            'tindakan'        => "Mengubah status akun '{$user->name}': " . ($user->is_active ? 'Aktif' : 'Nonaktif'),
            'data_lama'       => ['is_active' => $oldStatus],
            'data_baru'       => ['is_active' => $user->is_active],
            'waktu_perubahan' => now(),
        ]);

        // Force logout active session if deactivated
        if (!$user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.users.index')
            ->with('success', "Akun user '{$user->name}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $deletedInfo = ['id' => $user->id, 'name' => $user->name, 'username' => $user->username];
        $user->delete();

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Manajemen User',
            'tindakan'        => "Menghapus akun user: {$deletedInfo['name']} ({$deletedInfo['username']})",
            'data_lama'       => $deletedInfo,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
