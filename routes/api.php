<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Roles\RoleController;
use App\Http\Controllers\Permissions\PermissionController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Public routes
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('forgetpassword', [UserController::class, 'forgetpassword']);
Route::post('resetpassword', [UserController::class, 'resetpassword']);
//Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('emailverification', [UserController::class, 'emailverification']);
    Route::post('sendemailverification', [UserController::class, 'sendemailverification']);
    Route::get('profile', [UserController::class, 'getprofile']);
    Route::patch('profile', [UserController::class, 'updateprofile']);
    //Protected Roles routes 
    Route::group(['prefix' => 'roles'], function () {
        Route::get('', [RoleController::class, 'index']);
        Route::post('', [RoleController::class, 'update']);
        Route::post('/give-permission-to-role/{role}', [RoleController::class, 'givePermissionToRole']);
        Route::post('/give-permissions-to-roles/{role}', [RoleController::class, 'givePermissionsToRoles']);
        Route::post('/delete-permission-from-role/{role}', [RoleController::class, 'deletePermissionToRole']);
    });
    //Protected Permissions routes
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('', [PermissionController::class, 'index']);
        Route::post('', [PermissionController::class, 'update']);
    });
    //Protected Assign Roles & Permissions routes
    Route::group(['prefix' => 'users'], function () {
        Route::post('assign-role-to-user/{id}', [UserController::class, 'assignRoleToUser']);
        Route::post('remove-role-from-user/{id}', [UserController::class, 'deleteRoleFromUser']);
        Route::get('get-all-user-permissions/{id}', [UserController::class, 'getAllUserPermissions']);
        Route::get('get-all-user-roles/{id}', [UserController::class, 'getRolesNameForUser']);
        Route::get('get-all-user-for-specific-role-permission/', [UserController::class, 'filterUserWithRoles']);
    });
});


//Protected routes 
// Route::group(['middleware' => 'auth:sanctum'], function () {
//     Route::get('/create-article', function () {
//         $user = User::find(Auth::user()->id);
//         if(!$user->can('create article')){
//             return response()->json([
//                 'message' => 'You are not authorized to do this acttion ',
//                 'status' => false,
//                 'statusCode'=>401,
//                 'data' => null,
//             ]);
//         }
//         return response()->json(['message' => 'You allow to create articles ', 'statusCode' => 200], 200);
//     })->middleware('permission:create article');
//     Route::get('/edit-article', function () {
//         $user = User::find(Auth::user()->id);
//         if(!$user->can('edit article')){
//             return response()->json([
//                 'message' => 'You are not authorized to do this acttion & U will be redirected to Home page after 2 seconds',
//                 'status' => false,
//                 'statusCode'=>401,
//                 'data' => null,
//             ],401);
//         }
//         return response()->json(['message' => 'You allow to edit articles ', 'statusCode' => 200], 200);
//     });
//     Route::get('/delete-article', function () {
//         return response()->json(['message' => 'You allow to delete articles ', 'statusCode' => 200], 200);
//     })->middleware('can:delete article');
// });

// Protected Article routes
Route::group(['prefix'=>'articles','middleware'=>'auth:sanctum'],function(){
    Route::controller(ArticleController::class)->group(function(){
        Route::get('/','index');
        Route::post('/','store');
        Route::get('/{id}','show');
        Route::patch('/{id}','update');
        Route::delete('/{id}','destroy');
    });
});