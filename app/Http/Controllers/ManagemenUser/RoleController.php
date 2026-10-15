<?php

namespace App\Http\Controllers\ManagemenUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware():array
    {
        return [
            new Middleware('permission:view role', only: ['index']),
            new Middleware('permission:edit role', only: ['edit', 'update']),
            new Middleware('permission:create role', only: ['create', 'store']),
            new Middleware('permission:delete role', only: ['destroy']),
        ];
    }

    public function index(){
        $roles = Role::orderBy('name','ASC')->paginate(10);
        return view('managemenuser.role.index',[
            'roles' => $roles
        ]);
    }

    public function create(){

        $permissions = Permission::orderBy('name','ASC')->get();

        return view('managemenuser.role.create',[
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:roles|min:3',
            'permission' => 'required|array|min:1'
        ]);

        if($validator->passes()){
            $role = Role::create(['name'=> $request->name]);
            if(!empty($request->permission)){
                foreach($request->permission as $name){
                    $role->givePermissionTo($name);
                }
            }
            return redirect()->route('roles.index')->with('success','Roles added successfully');
        }
        else{
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){
        $role = Role::findOrFail($id);
        $hasRoles = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name','ASC')->get();
        
        return view('managemenuser.role.edit',[
            'permissions' => $permissions,
            'hasRoles' => $hasRoles,
            'role' => $role
        ]);
    }

    public function update($id, Request $request){
    $role = Role::findOrFail($id);

    // Role inti tidak boleh di-rename agar hak akses Admin tidak hilang.
    if (strtolower($role->name) === 'admin' && $request->name !== $role->name) {
        return redirect()->route('roles.edit', $id)
                         ->withErrors(['name' => 'Role Admin tidak dapat diubah namanya'])
                         ->withInput();
    }

    $validator = Validator::make($request->all(),[
        'name' => 'required|min:3|unique:roles,name,'.$id.',id',
        'permission' => 'required|array|min:1'
    ],[
        'permission.required' => 'Permission tidak boleh kosong. Minimal pilih salah satu.',
        'permission.min' => 'Minimal pilih salah satu permission.',
    ]);

    if($validator->fails()){
        return redirect()->route('roles.edit', $id)
                         ->withErrors($validator)
                         ->withInput();
    }

    $role->update(['name' => $request->name]);

    $role->syncPermissions($request->permission);

    return redirect()->route('roles.index')->with('success','Role updated successfully');
    }


    public function destroy(string $id)
    {
    $role = Role::findOrFail($id);

    // Role inti tidak boleh dihapus agar sistem tidak terkunci.
    if (strtolower($role->name) === 'admin') {
        return redirect()->route('roles.index')
            ->with('error', 'Role Admin tidak dapat dihapus');
    }

    $role->delete();

    return redirect()->route('roles.index')
        ->with('success', 'Role deleted successfully');
    }
}
