<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Events\UserNewLoginEvent;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\EmailVerificationEvent;
use App\Events\ResetPasswordEvent;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResrouce;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private $otp;
    public function __construct()
    {
        $this->otp = new Otp;
    }
    // ----------------------------- Register & Login & Logout ----------------------------- 
    public function register(UserRequest $request)
    {
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        event(new EmailVerificationEvent($user));
        return response()->json([
            'data' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
            'message' => 'Request was successful',
        ]);
    }
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // if(!Auth::attempt([$request->only('email','password')])){
            return response()->json([
                'message' => 'Credentials do not match',
                'error' => 'Request Error',
                'statusCode' => 401,
            ]);
        }
        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();
        event(new UserNewLoginEvent($user));
        // try {
        // } catch (\Exception $e) {}
        return response()->json([
            'data' => $user,
            'token' => $user->createToken('Api Token of ' . $user->name, expiresAt: now()->addDay())->plainTextToken,
            'message' => 'Request was successful',
        ]);
    }
    public function logout()
    {
        $user = User::find(Auth::user()->id);
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'You have successfully been logged out and your token has been deleted',

        ]);
    }
    // ----------------------------- Register & Login & Logout ----------------------------- 
    // ----------------------------- Email Verification ----------------------------- 
    public function emailverification(Request $request)
    {
        $otpValidate = $this->otp->validate($request->email, $request->otp);
        if (!$otpValidate->status) {
            return response()->json([
                'error' => $otpValidate,
                'message' => 'Otp is not valid',
            ], 401);
        }
        $user = User::where('email', '=', $request->email)->first();
        $user->update([
            'email_verified_at' => now(),
        ]);
        return response()->json([
            'data' => $user,
            'message' => 'User Email is verified',
            'statusCode' => 200,
        ]);
    }
    public function sendemailverification(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        event(new EmailVerificationEvent($user));
        return response()->json([
            'message' => 'Email Verification Sent',
            'statusCode' => 200,
        ]);
    }
    // ----------------------------- Email Verification ----------------------------- 
    // ----------------------------- ForgetPassword | Reset Password ----------------------------- 
    public function forgetpassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email']
        ]);
        $user = User::where('email', '=', $request->email)->first();
        event(new ResetPasswordEvent($user));
        return response()->json([
            'message' => 'Reset Password Code Sent',
            'statusCode' => 200,
        ]);
    }
    public function resetpassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'confirmed']
        ]);
        $otpValidated = $this->otp->validate($request->email, $request->otp);
        if (!$otpValidated->status) {
            return response()->json([
                'error' => $otpValidated,
                'message' => 'Otp is not valid',
            ], 401);
        }
        $user = User::where('email', '=', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Password has update',
            'data' => $user,
            'statusCode' => 200
        ]);
    }
    // ----------------------------- ForgetPassword | Reset Password ----------------------------- 
    // ----------------------------- User Profile & Update User Profile ----------------------------- 
    public function getprofile(Request $request)
    {
        // check if current user session is equal to sent in request [public function getprofile(Request $request , $id)]
        // if(Auth::user()->id !== $request->user()->id){
        //     return response()->json(
        //         ['message'=>'You are not authorized to make this request']
        //     );
        // }
        // return Auth::user(); // == $request->user();
        return UserResrouce::make($request->user());
    }
    public function updateprofile(Request $request)
    {
        // $user = $request->user();
        $user = User::find(Auth::user()->id);
        $validatedData = $request->validate([
            'name' => ['string', 'min:6',],
            'email' => ['string', 'email', 'unique:users,email,' . $user->id]
        ]);
        $user->update($validatedData);
        $user = $user->refresh();
        return response()->json([
            'data' => $user,
            'message' => 'User Data updated Successfully',
            'statusCode' => 200,
        ], 200);
    }
    // ----------------------------- User Profile & Update User Profile ----------------------------- 
    // ----------------------------- Assign Role To User | Remove Role From User ----------------------------- 
    public function assignRoleToUser($id, Request $request)
    {
        $user = User::find($id);
        if ($user) {
            $request->validate([
                'role_name' => ['required', 'string', 'exists:roles,name'],
            ]);
            $role = Role::where('name', '=', $request->role_name)->first();
            if ($role) {
                //--------#>[IF] U have multliple roles just add roles instead of $role ---->> //$user->assignRole([$role1,$role2,$role3]); 
                $user->assignRole($role);
                return response()->json([
                    'data' => $user,
                    'role' => $role,
                    'message' => 'Role has assigned to User Successfully',
                    'statusCode' => 200,
                ], 200);
            }
            return response()->json([
                'message' => 'Role Not Found',
                'status' => false,
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        }
        return response()->json([
            'message' => 'User Not Found',
            'status' => false,
            'statusCode' => Response::HTTP_NOT_FOUND,
        ]);
    }
    public function deleteRoleFromUser($id, Request $request)
    {
        $user = User::find($id);
        if ($user) {
            $request->validate([
                'role_name' => ['required', 'string', 'exists:roles,name'],
            ]);
            $role = Role::where('name', '=', $request->role_name)->first();
            if ($role) {
                //--------#>[IF] U have multliple roles just add roles instead of $role ---->> //$user->syncRoles([$role1,$role2,$role3]); 
                $user->removeRole($role);
                return response()->json([
                    'data' => $user,
                    'message' => 'Role has removed from User Successfully',
                    'statusCode' => 200,
                ], 200);
            }
            return response()->json([
                'message' => 'Role Not Found',
                'status' => false,
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        }
        return response()->json([
            'message' => 'User Not Found',
            'status' => false,
            'statusCode' => Response::HTTP_NOT_FOUND,
        ]);
    }
    // ----------------------------- Assign Role To User | Remove Role From User ----------------------------- 
    // ----------------------------- Get All User Roles | Get All User Permissions ----------------------------- 
    public function getAllUserPermissions($id)
    {
        $user = User::find($id);
        // $permissionNames = $user->getPermissionNames(); // get permissions for user from permissions table only
        // $permissionNames = $user->permissions; // get collection of permission objects for user
        // $permissionNames = $user->getDirectPermissions(); // get directly from permissions table
        // $permissionNames = $user->getPermissionsViaRoles(); // get permissions for user via roles table
        $permissionNames = $user->getAllPermissions(); // get all permissions for user
        return $permissionNames;
    }
    public function getRolesNameForUser($id){
        $user = User::find($id);
        $roles = $user->getRoleNames(); 
        return $roles;
    }
    public function filterUserWithRoles(){
        $users = User::role('moderator')->get();
        // $nonEditors = User::withoutRole('editor')->get();
        $users = User::permission('create article')->get();
        // $usersWhoCannotEditArticles = User::withoutPermission('edit articles')->get();
        return $users;
    }
    // ----------------------------- Get All User Roles | Get All User Permissions ----------------------------- 
}
