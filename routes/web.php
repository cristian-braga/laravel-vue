<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardStatsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// });

Route::middleware('auth')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/api/users', 'index')->name('users.index');
        Route::post('/api/users', 'store')->name('users.store');
        Route::patch('/api/users/{user}/change-role', 'changeRole')->name('users.changeRole');
        Route::put('/api/users/{user}', 'update')->name('users.update');
        Route::delete('/api/users/{user}', 'destroy')->name('users.destroy');
        Route::delete('/api/users', 'bulkDelete')->name('users.bulkDelete');
    });

    Route::controller(AppointmentController::class)->group(function () {
        Route::get('/api/appointments', 'index')->name('appointments.index');
        Route::get('/api/appointments-status', 'getStatusWithCount')->name('appointments.getStatusWithCount');
        Route::post('/api/appointments/store', 'store')->name('appointments.store');
        Route::get('/api/appointments/{appointment}/edit', 'edit')->name('appointments.edit');
        Route::put('/api/appointments/{appointment}', 'update')->name('appointments.update');
        Route::delete('/api/appointments/{appointment}', 'destroy')->name('appointments.destroy');
    });

    Route::controller(ClientController::class)->group(function () {
        Route::get('/api/clients', 'index')->name('clients.index');
    });

    Route::controller(DashboardStatsController::class)->group(function () {
        Route::get('/api/stats/appointments', 'appointments')->name('dashboard.appointments');
        Route::get('/api/stats/users', 'users')->name('dashboard.users');
    });

    Route::controller(SettingController::class)->group(function () {
        Route::get('/api/settings', 'index')->name('settings.index');
        Route::post('/api/settings', 'update')->name('settings.update');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/api/profile', 'index')->name('profile.index');
        Route::put('/api/profile', 'update')->name('profile.update');
        Route::post('/api/upload-profile-image', 'uploadImage')->name('profile.uploadImage');
        Route::post('/api/change-password', 'changePassword')->name('profile.changePassword');
    });
});

Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
