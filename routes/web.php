<?php

use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\User\PermissionAssignmentController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
    ], function () {

        Route::middleware(['auth', 'active', 'twofactor'])->group(function () {
            // Dashboard - blank page
            Route::get('/', function () {
                return view('dashboard');
            })->name('index');

            // Profile Routes
            Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update')->middleware('throttle:30,1');
            Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');
            
            // Two-factor authentication - 5 requests per minute
            Route::post('/profile/two-factor/enable', [TwoFactorAuthenticationController::class, 'store'])->name('two-factor.enable')->middleware('throttle:5,1');
            Route::post('/profile/two-factor/confirm', [TwoFactorAuthenticationController::class, 'confirm'])->name('two-factor.confirm')->middleware('throttle:5,1');
            Route::delete('/profile/two-factor', [TwoFactorAuthenticationController::class, 'destroy'])->name('two-factor.disable')->middleware('throttle:5,1');
            
            // Settings Group: Users, Permissions, and System Settings
            Route::name('settings.')->group(function () {
                
                // System Settings - Company Info
                Route::get('/company', [SystemSettingController::class, 'companyInfo'])->name('company');
                
                // Cache refresh - 5 requests per minute (resource intensive)
                Route::post('/refresh-cache', [SystemSettingController::class, 'refreshCache'])->name('refresh-cache')->middleware('throttle:5,1');
                Route::put('/{category}/{key}', [SystemSettingController::class, 'update'])->name('update')->middleware('throttle:30,1');
                Route::post('/logo/delete', [SystemSettingController::class, 'deleteLogo'])->name('delete-logo')->middleware('throttle:10,1');

                // Permission Assignment Routes (Owner only)
                Route::prefix('permissions')->name('permissions.')->middleware('can:manage roles')->group(function () {
                    Route::get('/', [PermissionAssignmentController::class, 'index'])->name('index');
                    Route::get('/role/{role}', [PermissionAssignmentController::class, 'showRole'])->name('role');
                    // Permission operations - Very strict: 5 requests per minute
                    Route::post('/role/{role}/assign', [PermissionAssignmentController::class, 'assignPermissions'])->name('assign')->middleware('throttle:5,1');
                    Route::post('/role/{role}/revoke', [PermissionAssignmentController::class, 'revokePermissions'])->name('revoke')->middleware('throttle:5,1');
                });

                // User Routes
                Route::prefix('users')->name('users.')->group(function () {
                    Route::middleware('can:view user')->get('/', [UserController::class, 'list'])->name('index');
                    Route::middleware('can:add user')->post('/store', [UserController::class, 'store'])->name('store')->middleware('throttle:30,1');
                    Route::middleware('can:update user')->get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
                    Route::patch('/update/{user}', [UserController::class, 'update'])->name('update')->middleware('throttle:30,1');
                    Route::middleware('can:delete user')->post('/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status')->middleware('throttle:10,1');
                });
            });

        });

        require __DIR__.'/auth.php';

        Route::middleware('auth')->group(function () {
            Route::get('/two-factor/challenge', [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
            // Two-factor challenge - 10 attempts per minute
            Route::post('/two-factor/challenge', [TwoFactorChallengeController::class, 'store'])->name('two-factor.challenge.store')->middleware('throttle:10,1');
        });

    });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

