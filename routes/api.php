<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['web'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('add/notes', [NoteController::class, 'store'])->middleware('trackGuestActivity');
});