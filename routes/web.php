<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', 'Auth\AuthController@register');
Route::post('/login', 'Auth\AuthController@login');
Route::post('/logout', 'Auth\AuthController@logout');
