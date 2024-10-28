<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// });

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

Route::get('{view}', ApplicationController::class)->where('view', '(.*)');