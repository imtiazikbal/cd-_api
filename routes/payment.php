<?php

use App\Http\Controllers\BkashController;
use App\Http\Controllers\NagadController;
use App\Http\Controllers\SslCommerzController;
use Illuminate\Support\Facades\Route;

Route::any('ssl/callback/{status}', [SslCommerzController::class, 'callback'])->name('ssl.callback');
Route::any('bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');
Route::any('nagad/callback', [NagadController::class, 'callback'])->name('nagad.callback');
