<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/users', function () {
    return User::all();
});

Route::post('/users', function (Request $request) {
    return User::create($request->all());
});