<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ExtractVoiceController;
use App\Http\Controllers\GenerateContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    /**
     **************************************************************************************
     * API Routes
     **************************************************************************************
     */
    Route::middleware('auth')->group(function () {
        /**
         **************************************************************************************
         * Brand Voice Routes
         **************************************************************************************
         */
        Route::name('brand-voice.')->prefix('brand-voice')->group(function () {
            /**
             * Extract Voice Profile
             * Analysis of 3-5 text samples to create a linguistic DNA.
             */
            Route::post('extract', ExtractVoiceController::class)->name('extract');

            /**
             * Generate Content
             * Uses a profile to generate text with the Quality Gate loop.
             */
            Route::post('generate/{profile}', GenerateContentController::class)->name('generate');
        });
    });

    /**
     **************************************************************************************
     * Auth Routes
     **************************************************************************************
     */
    Route::name('auth.')->prefix('auth')->group(function () {
        // Login
        Route::post('login', LoginController::class)
            ->middleware('guest')
            ->name('login');
        // Logout
        Route::post('logout', LogoutController::class)
            ->middleware(['auth'])
            ->name('logout');
    });
});

Route::get('{any}', fn () => view('app'))
    ->where('any', '.*')
    ->name('spa');
