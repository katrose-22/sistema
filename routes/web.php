<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\PasanteController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');



    Route::get('/pasantes', [PasanteController::class, 'index']);
    Route::get('/pasantes/create', [PasanteController::class, 'create']);
    Route::post('/pasantes', [PasanteController::class, 'store']);
    Route::get('/pasantes/{id}/edit', [PasanteController::class, 'edit']);
    Route::put('/pasantes/{id}', [PasanteController::class, 'update']);
    Route::delete('/pasantes/{id}', [PasanteController::class, 'destroy']);
require __DIR__.'/settings.php';
