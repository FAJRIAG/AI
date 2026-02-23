<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicChatController;
use App\Http\Controllers\{DashboardController, ProjectController, ChatSessionController, ChatController};

Route::get('/', [PublicChatController::class, 'index'])->name('public.chat');
Route::post('/public/new', [PublicChatController::class, 'new'])->name('public.new');
Route::get('/public/switch/{sid}', [PublicChatController::class, 'switch'])->name('public.switch');
Route::post('/public/rename/{sid}', [PublicChatController::class, 'rename'])->name('public.rename');
Route::post('/public/delete/{sid}', [PublicChatController::class, 'delete'])->name('public.delete');
Route::post('/public/stream/{sid}', [PublicChatController::class, 'stream'])->name('public.stream');

Route::view('/terms', 'public.terms')->name('terms');
Route::view('/privacy', 'public.privacy')->name('privacy');
Route::view('/pricing', 'auth.register')->name('pricing');

Route::middleware(['auth', 'vip'])->group(function () {
    Route::get('/vip', [DashboardController::class, 'index'])->name('vip.home');
    Route::get('/app', [DashboardController::class, 'index'])->name('chat.dashboard');


    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/projects/{project}/sessions', [ChatSessionController::class, 'store'])->name('sessions.store');
    Route::patch('/sessions/{session}', [ChatSessionController::class, 'update'])->name('sessions.update');
    Route::delete('/sessions/{session}', [ChatSessionController::class, 'destroy'])->name('sessions.destroy');
    Route::get('/sessions/{session}/messages', [ChatSessionController::class, 'fetch'])->name('sessions.fetch');

    Route::post('/sessions/{session}/stream', [ChatController::class, 'stream'])->name('chat.stream');
});

// Logout Route (GET for stability)
Route::get('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']); // Keep POST support

// Breeze default "dashboard" -> arahkan kemana pun yang kamu mau
Route::get('/dashboard', fn() => redirect()->route('vip.home'))->name('dashboard');

// auth routes Breeze
if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}
