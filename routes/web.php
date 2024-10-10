<?php

use App\Http\Helpers\Utils;
use App\Http\Controllers\Auth0Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;

// Define the root path route
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [Auth0Controller::class, 'login'])->name('login');
Route::get('/callback', [Auth0Controller::class, 'callback'])->name('callback');
Route::get('/logout', [Auth0Controller::class, 'logout'])->name('logout');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');