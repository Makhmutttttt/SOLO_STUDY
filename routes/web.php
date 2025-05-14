<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PassController;
use App\Http\Controllers\AuthController;

// Главная страница
// Route::redirect('/', '/login');

// // Аутентификация
// Route::middleware('guest')->group(function () {
//     Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
//     Route::post('/register', [AuthController::class, 'register'])->name('register');

//     Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
//     Route::post('/login', [AuthController::class, 'login'])->name('login');
// });

// // Выход
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// // Защищённые маршруты (только для авторизованных)
// Route::middleware(['auth'])->group(function () {

//     // Управление тестами
//     Route::prefix('tests')->name('tests.')->group(function () {
//         Route::get('/', [TestController::class, 'index'])->name('index');
//         Route::get('/create', [TestController::class, 'create'])->name('create');
//         Route::post('/generate', [TestController::class, 'generate'])->name('generate');
//         Route::get('/status/{id}', [TestController::class, 'checkTestStatus'])->name('status');
//     });

//     // Прохождение тестов
//     Route::prefix('test')->name('test.')->group(function () {
//         Route::post('/{test}/start', [PassController::class, 'start'])->name('start');
//         Route::get('/question', [PassController::class, 'showQuestion'])->name('question');
//         Route::post('/answer', [PassController::class, 'processAnswer'])->name('answer');
//         Route::get('/finish', [PassController::class, 'finish'])->name('finish');
//     });
// });




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
});

Route::get('/tests/status/{id}', action: [TestController::class, 'checkTestStatus'])->name('tests.status');
Route::middleware(['auth'])->group(function () {
    Route::post('/tests/{test}/start', [PassController::class, 'start'])->name('test.start');
    Route::get('/test/question', [PassController::class, 'showQuestion'])->name('test.question');
    Route::post('/test/answer', [PassController::class, 'processAnswer'])->name('test.answer');
    Route::get('/test/finish', [PassController::class, 'finish']) ->name('test.finish');});