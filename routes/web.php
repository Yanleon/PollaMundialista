<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MatchGameController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Participant\DashboardController as ParticipantDashboardController;
use App\Http\Controllers\Participant\PredictionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('participant.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    Route::middleware('role:participant')->prefix('participant')->name('participant.')->group(function () {
        Route::get('/dashboard', [ParticipantDashboardController::class, 'index'])->name('dashboard');
        Route::get('/predictions', [PredictionController::class, 'index'])->name('predictions.index');
        Route::post('/match-games/{matchGame}/prediction', [PredictionController::class, 'upsert'])->name('predictions.upsert');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('teams', TeamController::class)->except(['show']);
        Route::get('/match-games/bracket', [MatchGameController::class, 'bracket'])->name('match-games.bracket');
        Route::resource('match-games', MatchGameController::class)->parameters(['match-games' => 'matchGame'])->except(['show']);
        Route::patch('/match-games/{matchGame}/result', [MatchGameController::class, 'updateResult'])->name('match-games.update-result');
        Route::post('/match-games/notify-today', [MatchGameController::class, 'notifyToday'])->name('match-games.notify-today');
        Route::get('/prediction-report', [UserController::class, 'predictionReport'])->name('prediction-report.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
