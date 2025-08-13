<?php

use App\Models\Calculation;
use Illuminate\Support\Facades\Route;

Route::redirect('/panel/login', '/login')->name('login');

Route::get('/calculation/print/{calculation}', function (Calculation $calculation) {
    return view('calculation-print', [
        'calculation' => $calculation,
    ]);
})->name('calculation.print');
