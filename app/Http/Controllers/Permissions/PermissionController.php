<?php

namespace App\Http\Controllers\Permissions;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    public function index(){
        $permissions = Permission::all();
        return response()->json([
            'data' => $permissions,
            'message'=> 'Request was successful',
            'status'=>true,
            'statusCode'=>Response::HTTP_OK,
        ]);
    }

    public function update(Request $request) {
        $request->validate([
            'name'=>['required','string','unique:permissions,name'],
            'guard_name'=>['required','string'],
        ]);
        $permission = Permission::create([
            'name'=>$request->name,
            'guard_name'=>$request->guard_name
        ]);
        return response()->json([
            'data'=> $permission,
            'message'=> 'Request Was successful',
            'status'=>true,
            'statusCode'=>Response::HTTP_CREATED,
        ]);
    }
}
