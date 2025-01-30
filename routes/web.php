<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home');
});

Route::inertia('/pricing', 'Pricing')->name('pricing');
