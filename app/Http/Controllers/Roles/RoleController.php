<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            'data' => $roles,
            'message' => 'Request was successful',
            'status' => true,
            'statusCode' => Response::HTTP_OK,
        ]);
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:roles,name'],
            'guard_name' => ['required', 'string'],
        ]);
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);
        return response()->json([
            'data' => $role,
            'message' => 'Request Was successful',
            'status' => true,
            'statusCode' => Response::HTTP_CREATED,
        ]);
    }
    //------------------------------ Give Permission To Role & Delete Permission From Role------------------------------------------
    // 1- Give Single permission to role
    public function givePermissionToRole(Role $role,Request $request)
    {
        $request->validate([
            'permission_name' => ['required','exists:permissions,name','string']
        ]);
        $role = Role::where('name','=',$role->name)->first();
        $permission = Permission::where('name','=',$request->permission_name)->first();
        if($permission){
            $role->givePermissionTo($permission->name);
            // [OR] do this line ------>> // $permission->assignRole($role);
            return response()->json([
                'data' => $role,
                'permissions'=>$permission,
                'message' => 'Request Was successful',
                'status' => true,
                'statusCode' => Response::HTTP_OK,
            ]);
        }
        return response()->json([
            'message'=>'Permission Not Found',
            'status'=>false,
            'statusCode'=>Response::HTTP_NOT_FOUND,
        ]);
    }
    // 2- Give Multiple permissions to role
    public function givePermissionsToRoles(Role $role,Request $request)
    {
        $request->validate([
            'permissions'=>['','required'],
            'permissions.*'=>['string','exists:permissions,name','required']
        ]);
        $role = Role::where('name','=',$role->name)->first();
        $permissions = Permission::whereIn('name',$request->permissions)->get();
        if($role){
            $role->syncPermissions($permissions);
            // [IF] U want to giver roles to specific permissions write this ------->> //$permission->syncRoles($roles);
            return response()->json([
                'data' => $role,
                'permissions'=>$permissions,
                'message' => 'Request Was successful',
                'status' => true,
                'statusCode' => Response::HTTP_OK,
            ]);
        }
        return response()->json([
            'message'=>'Role Not Found',
            'status'=>false,
            'statusCode'=>Response::HTTP_NOT_FOUND,
        ]);
    }
    // 3- Delete Permission From Role
    public function deletePermissionToRole(Role $role,Request $request){
        $request->validate([
            'permission_name' => ['required','exists:permissions,name','string']
        ]);
        $role = Role::where('name','=',$role->name)->first();
        $permission = Permission::where('name','=',$request->permission_name)->first();
        if($permission){
            $role->revokePermissionTo($permission);
            // [OR] U can write this line ---------->> // $permission->removeRole($role);
            return response()->json([
                'data' => $role,
                'message' => 'Request Was successful | Permission Was deleted',
                'status' => true,
                'statusCode' => Response::HTTP_OK,
            ]);
        }
        return response()->json([
            'message'=>'Permission Not Found',
            'status'=>false,
            'statusCode'=>Response::HTTP_NOT_FOUND,
        ]);
    }
    //------------------------------ Give Permission To Role & Delete Permission From Role------------------------------------------
}
