<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ENDPOINTS ──────────────────────────────────────────
// Tidak memerlukan token. Hanya operasi baca.
Route::get('/posts',          [PostController::class, 'index']);
Route::get('/posts/{post}',   [PostController::class, 'show']);
Route::get('/posts/{post}/comments',         [CommentController::class, 'index']);
Route::get('/posts/{post}/comments/{comment}', [CommentController::class, 'show']);

// ─── AUTH ENDPOINTS ────────────────────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);

// ─── PRIVATE ENDPOINTS ─────────────────────────────────────────
// Semua route di sini wajib menyertakan token Sanctum di header.
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Editor & Admin: buat dan ubah post milik sendiri
    Route::middleware('role:admin|editor')->group(function () {
        Route::post('/posts',             [PostController::class, 'store']);
        Route::patch('/posts/{post}',     [PostController::class, 'update']);
        Route::post('/posts/{post}/comments',              [CommentController::class, 'store']);
        Route::patch('/posts/{post}/comments/{comment}',   [CommentController::class, 'update']);
    });

    // Admin only: hapus post dan komentar
    Route::middleware('role:admin')->group(function () {
        Route::delete('/posts/{post}',                     [PostController::class, 'destroy']);
        Route::delete('/posts/{post}/comments/{comment}',  [CommentController::class, 'destroy']);
    });
});
