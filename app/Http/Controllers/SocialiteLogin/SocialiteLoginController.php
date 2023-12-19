<?php

namespace App\Http\Controllers\SocialiteLogin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\UserNewLoginEvent;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialiteLoginController extends Controller
{
    //------------------------------- Start Github ----------------------------------------
    public function redirectToGithubProvider()
    {
        return Socialite::driver('github')->redirect();
    }
    public function handleGithubProviderCallback(Request $request)
    {
        $socialiteUser = Socialite::driver('github')->user();
        // $socialiteUser->token
        $user = User::where([
            'provider'=>'github',
            'provider_id'=>$socialiteUser->getId(),
            ])->first();
            if(!$user){
                $createdUser = User::create([
                    'name'=>$socialiteUser->getName(),
                    'email'=>$socialiteUser->getEmail(),
                    'provider'=>'github',
                    'provider_id'=>$socialiteUser->getId(),
                'email_verified_at'=>now(),
            ]);
            $user = $createdUser;
            // event(new UserNewLoginEvent($createUser));
        }
        $user->tokens()->delete();
        return response()->json([
            'data'=> $user,
            'githubToken'=>$socialiteUser->token,
            'token'=> $user->createToken('Api Token of ' . $socialiteUser->getName(), expiresAt: now()->addDay())->plainTextToken,
            'message' => 'Request was successful',
            'statusCode' => 200
        ]);
    }
    //------------------------------- End Github ----------------------------------------
    //------------------------------- Start Google ----------------------------------------
    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleProviderCallback(Request $request)
    {
        $socialiteUser = Socialite::driver('google')->user();
        // $socialiteUser->token
        // dd($socialiteUser);
        $user = User::where([
            'provider'=>'google',
            'provider_id'=>$socialiteUser->getId(),
            ])->first();
            if(!$user){
                $createdUser = User::create([
                    'name'=>$socialiteUser->getName(),
                    'email'=>$socialiteUser->getEmail(),
                    'provider'=>'google',
                    'provider_id'=>$socialiteUser->getId(),
                'email_verified_at'=>now(),
            ]);
            $user = $createdUser;
            // event(new UserNewLoginEvent($createUser));
        }
        $user->tokens()->delete();
        return response()->json([
            'data'=> $user,
            'googleToken'=>$socialiteUser->token,
            'token'=> $user->createToken('Api Token of ' . $socialiteUser->getName(), expiresAt: now()->addDay())->plainTextToken,
            'message' => 'Request was successful',
            'statusCode' => 200
        ]);
    }
    //------------------------------- End Google ----------------------------------------
    //------------------------------- Start Facebook ----------------------------------------
    public function redirectToFacebookProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function handleFacebookProviderCallback(Request $request)
    {
        $socialiteUser = Socialite::driver('facebook')->user();
        // $socialiteUser->token
        dd($socialiteUser);
        $user = User::where([
            'provider'=>'facebook',
            'provider_id'=>$socialiteUser->getId(),
            ])->first();
            if(!$user){
                $createdUser = User::create([
                    'name'=>$socialiteUser->getName(),
                    'email'=>$socialiteUser->getEmail(),
                    'provider'=>'facebook',
                    'provider_id'=>$socialiteUser->getId(),
                'email_verified_at'=>now(),
            ]);
            $user = $createdUser;
            // event(new UserNewLoginEvent($createUser));
        }
        $user->tokens()->delete();
        return response()->json([
            'data'=> $user,
            'facebookToken'=>$socialiteUser->token,
            'token'=> $user->createToken('Api Token of ' . $socialiteUser->getName(), expiresAt: now()->addDay())->plainTextToken,
            'message' => 'Request was successful',
            'statusCode' => 200
        ]);
    }
    //------------------------------- End Facebook ----------------------------------------
    //------------------------------- Start Linkedin ----------------------------------------
    public function redirectToLinkedinProvider()
    {
        return Socialite::driver('linkedin')->redirect();
    }
    public function handleLinkedinProviderCallback(Request $request)
    {
        $socialiteUser = Socialite::driver('linkedin')->user();
        // $socialiteUser->token
        dd($socialiteUser);
        $user = User::where([
            'provider'=>'linkedin',
            'provider_id'=>$socialiteUser->getId(),
            ])->first();
            if(!$user){
                $createdUser = User::create([
                    'name'=>$socialiteUser->getName(),
                    'email'=>$socialiteUser->getEmail(),
                    'provider'=>'linkedin',
                    'provider_id'=>$socialiteUser->getId(),
                'email_verified_at'=>now(),
            ]);
            $user = $createdUser;
            // event(new UserNewLoginEvent($createUser));
        }
        $user->tokens()->delete();
        return response()->json([
            'data'=> $user,
            'linkedinToken'=>$socialiteUser->token,
            'token'=> $user->createToken('Api Token of ' . $socialiteUser->getName(), expiresAt: now()->addDay())->plainTextToken,
            'message' => 'Request was successful',
            'statusCode' => 200
        ]);
    }
    //------------------------------- End Linkedin ----------------------------------------

}
