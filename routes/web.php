<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

