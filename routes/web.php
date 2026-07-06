<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.sppg');
})->name('home');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

