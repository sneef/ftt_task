<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/currency/index', [App\Http\Controllers\CurrencyController::class, 'index'])->name('currency.index');
