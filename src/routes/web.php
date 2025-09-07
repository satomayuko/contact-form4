<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index']);
Route::post('/confirm', [ContactController::class, 'confirm']);
Route::get('/thanks', [ContactController::class, 'thanks']);

Route::middleware('auth')->group(function () {
    Route::get('/admin', [ContactController::class, 'admin'])->name('admin');
    Route::delete('/admin/{contact}', [ContactController::class, 'destroy'])->name('admin.destroy');
    Route::get('/admin/export', [ContactController::class, 'export'])->name('admin.export');
});