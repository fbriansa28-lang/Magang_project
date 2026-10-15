<?php

namespace App\Http\Controllers\ManagemenUser;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view permission', only: ['index']),
            new Middleware('permission:create permission', only: ['store']),
            new Middleware('permission:edit permission', only: ['edit', 'update']),
            new Middleware('permission:delete permission', only: ['destroy']),
        ];
    }

    /**
     * Tampilkan halaman daftar permission.
     */
    public function index(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $keyword = trim((string) $request->input('keyword', ''));

        if ($keyword !== '') {
            $permissions = Permission::getPermissionByKeyword($keyword);
        } else {
            $permissions = Permission::getPermissionByPage($page);
        }

        return view('managemenuser.permission.index', compact('permissions', 'keyword'));
    }

    /**
     * Tambah permission baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3',
        ], [
            'name.required' => 'Nama permission wajib diisi',
            'name.unique' => 'Nama permission sudah ada, gunakan nama lain',
            'name.min' => 'Nama permission minimal 3 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->route('permissions.index')
                ->withErrors($validator)
                ->withInput();
        }

        Permission::insertPermission($request->only('name'));

        return redirect()->route('permissions.index')
            ->with('success', 'Permission added successfully');
    }

    /**
     * Ambil detail permission lalu tampilkan form edit.
     */
    public function edit(string $permission_id)
    {
        $permission = Permission::getDetailPermission((int) $permission_id);

        return response()->json([
            'id' => $permission->id,
            'name' => $permission->name,
        ]);
    }

    /**
     * Simpan perubahan data permission.
     */
    public function update(string $permission_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:permissions,name,' . $permission_id . ',id',
        ], [
            'name.required' => 'Nama permission wajib diisi',
            'name.unique' => 'Nama permission sudah ada, gunakan nama lain',
            'name.min' => 'Nama permission minimal 3 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->route('permissions.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_permission_id', $permission_id);
        }

        Permission::updatePermission((int) $permission_id, $request->only('name'));

        return redirect()->route('permissions.index')
            ->with('success', 'Permission update successfully');
    }

    /**
     * Hapus permission.
     */
    public function destroy(string $permission_id)
    {
        Permission::deletePermission((int) $permission_id);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
