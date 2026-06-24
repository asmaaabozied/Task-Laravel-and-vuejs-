<?php

use Illuminate\Support\Facades\Route;

Route::get('/health/live', fn () => response('OK', 200));
Route::get('/health/ready', fn () => response('OK', 200));
Route::get('/health/startup', fn () => response('OK', 200));
