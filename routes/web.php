<?php

use App\Models\Calculation;
use App\ValueObjects\CalculationResult;
use Illuminate\Support\Facades\Route;

Route::redirect('/panel/login', '/login')->name('login');

Route::get('/calculation/print/{calculation}', function (Calculation $calculation) {
    return view('calculation-print', [
        'calculation' => $calculation,
        'calculationResult' => CalculationResult::make($calculation),
    ]);
    $html = view('calculation-print', [
        'calculation' => $calculation,
        'calculationResult' => CalculationResult::make($calculation),
    ])->render();

    // save to temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'calculation');
    file_put_contents($tempFile, $html);

    $outputFile = tempnam(sys_get_temp_dir(), 'calculation');

    shell_exec("prince $tempFile -o $outputFile");

    // render pdf in browsdr
    // return response()->download($outputFile, 'calculation.pdf');

    return response()->file($outputFile, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="calculation.pdf"',
    ]);
})->name('calculation.print');
