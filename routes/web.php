<?php

use App\Http\Controllers\API\V1\Client\Order\TrackingTimelineController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [\App\Http\Controllers\General\HomeController::class, 'index'])->name('home');

Route::get('/signup', [\App\Http\Controllers\Merchant\Auth\LoginController::class, 'index'])->name('merchant.register');
Route::post('/signup/store', [\App\Http\Controllers\Merchant\Auth\LoginController::class, 'register'])->name('merchant.register.store');
Route::get('/thank_you', [\App\Http\Controllers\General\HomeController::class, 'thankYou'])->name('thank_you');
Route::post('/pathao/webhook/{secret}', [WebhookController::class, 'pathaoWebhookHandler'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
Route::post('/redx/webhook/funnelliner', [WebhookController::class, 'redxWebhookHandler'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
Route::post('/fraud-detection/webhook', [WebhookController::class, 'fraudCheckWebhookHandler'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);