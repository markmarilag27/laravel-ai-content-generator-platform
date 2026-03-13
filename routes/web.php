<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\BrandVoiceProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignItemController;
use App\Http\Controllers\GenerateContentController;
use App\Http\Controllers\ListContentController;
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
             */
            Route::name('extract.')->prefix('extract')->group(function () {
                // List of extracts
                Route::get('', [BrandVoiceProfileController::class, 'index'])->name('index');
                // Store a new extract
                Route::post('', [BrandVoiceProfileController::class, 'store'])->name('store');
                // Show extract
                Route::get('{profile}', [BrandVoiceProfileController::class, 'show'])->name('show');
                // Update extract
                Route::patch('{profile}', [BrandVoiceProfileController::class, 'update'])->name('update');
                // Delete extract
                Route::delete('{profile}', [BrandVoiceProfileController::class, 'destroy'])->name('destroy');
            });

            /**
             * Content
             */
            Route::get('contents', ListContentController::class)->name('contents');
            Route::post('generate/{profile}', GenerateContentController::class)->name('contents.generate');
        });
        /**
         **************************************************************************************
         * Campaigns
         **************************************************************************************
         */
        Route::name('campaigns.')->prefix('campaigns')->group(function () {
            // List campaigns
            Route::get('/', [CampaignController::class, 'index'])->name('index');
            // Store new campaigns
            Route::post('{profile}', [CampaignController::class, 'store'])->name('store');
            // Show campaign
            Route::get('{campaign}', [CampaignController::class, 'show'])->name('show');
            // Delete campaign
            Route::delete('{campaign}', [CampaignController::class, 'destroy'])->name('destroy');
            // List campaign items
            Route::get('{campaign}/items', CampaignItemController::class)->name('items');
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
        // Current user
        Route::get('me', MeController::class)
            ->middleware('auth')
            ->name('me');
    });
});

Route::get('{any}', fn () => view('app'))
    ->where('any', '.*')
    ->name('spa');
