<?php

use App\Http\Controllers\Payments\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialiteLogin\SocialiteLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Socialite Login 
// # Github
Route::get('/login/github', [SocialiteLoginController::class, 'redirectToGithubProvider'])->name('github.login');
Route::get('/login/github/callback', [SocialiteLoginController::class, 'handleGithubProviderCallback']);
// # Github
// # Google
Route::get('/login/google', [SocialiteLoginController::class, 'redirectToGoogleProvider'])->name('google.login');
Route::get('/login/google/callback', [SocialiteLoginController::class, 'handleGoogleProviderCallback']);
// # Google
// # Facebook
Route::get('/login/facebook', [SocialiteLoginController::class, 'redirectToFacebookProvider'])->name('facebook.login');
Route::get('/login/facebook/callback', [SocialiteLoginController::class, 'handleFacebookProviderCallback']);
// # Facebook
// # Linkedin
Route::get('/login/linkedin', [SocialiteLoginController::class, 'redirectToLinkedinProvider'])->name('linkedin.login');
Route::get('/login/linkedin/callback', [SocialiteLoginController::class, 'handleLinkedinProviderCallback']);
// # Linkedin
// Socialite Login
// Payment Gateways
// # Paypal
Route::get('view-payment',function(){
    return View('payment');
});
Route::post('/payment',[PaymentController::class,'payment'])->name('paypal');
Route::get('/payment/success',[PaymentController::class,'success'])->name('success');
Route::get('/payment/cancel',[PaymentController::class,'cancel'])->name('cancel');
// # Paypal
// Payment Gateways