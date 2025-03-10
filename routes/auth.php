<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('curriculo/cadastro', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

});

Route::middleware('auth')->group(function () {
    Route::prefix('curriculo')->group(function () {
        Route::get('editar', [UserController::class, 'editCurriculo'])
        ->name('editar-curriculo');
        Route::post('alterar', [UserController::class, 'updateCurriculum'])
        ->name('alterar-curriculo');
    });
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
