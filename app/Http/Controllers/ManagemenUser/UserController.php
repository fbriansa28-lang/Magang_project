<?php

namespace App\Http\Controllers\ManagemenUser;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view user', only: ['index']),
            new Middleware('permission:edit user', only: ['edit', 'update']),
            new Middleware('permission:delete user', only: ['destroy']),
        ];
    }

    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::orderBy('name')->get();

        return view('managemenuser.user.index', compact('users', 'roles'));
    }

    /**
     * Ambil detail user lalu tampilkan form edit.
     */
    public function edit(string $user_id)
    {
        $user = User::getDetailUser((int) $user_id);
        $roles = Role::orderBy('name')->get();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'selected_roles' => $user->roles->pluck('name')->values(),
            'roles' => $roles->pluck('name')->values(),
        ]);
    }

    /**
     * Simpan perubahan data user.
     */
    public function update(string $user_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_user_id', $user_id);
        }

        User::updateUser((int) $user_id, $request->only('role'));

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Hapus user.
     */
    public function destroy(string $user_id)
    {
        $deleted = User::deleteUser((int) $user_id);

        if (!$deleted) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak ditemukan');
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
