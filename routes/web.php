<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PassController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MistakeWorkController;
use App\Http\Controllers\MistakeController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login'); // если маршрут login имеет имя
});
Route::get('/tests/create', [TestController::class, 'create'])->name('tests.create');
Route::post('/tests/generate', [TestController::class, 'generate'])->name('tests.generate');


Route::delete('/tests/{id}', [TestController::class, 'destroy'])->name('test.destroy');


// Route::post('/tests/{id}/start', [TestController::class, 'start'])->name('test.start');

// Route::match(['get', 'post'], '/tests/generate', [TestController::class, 'generate'])->name('tests.generate');



// Маршруты для аутентификации
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Маршруты для тестов (только для авторизованных пользователей)
Route::middleware(['auth'])->group(function () {
    Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
    Route::get('/tests/input-issues', [MistakeController::class, 'index'])->name('mistakes.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('auth');
    
    Route::prefix('mistakes')->middleware(['auth'])->group(function () {
        Route::get('pass', [MistakeWorkController::class, 'pass'])->name('mistakes.pass');
        Route::post('answer', [MistakeWorkController::class, 'submitAnswer'])->name('mistakes.answer');
        Route::get('results', [MistakeWorkController::class, 'results'])->name('mistakes.results');
    });
});

Route::get('/tests/status/{id}', action: [TestController::class, 'checkTestStatus'])->name('tests.status');
Route::middleware(['auth'])->group(function () {
    Route::post('/tests/{test}/start', [PassController::class, 'start'])->name('test.start');
    Route::get('/test/question', [PassController::class, 'showQuestion'])->name('test.question');
    Route::post('/test/answer', [PassController::class, 'processAnswer'])->name('test.answer');
    Route::get('/test/finish', [PassController::class, 'finish']) ->name('test.finish');});



