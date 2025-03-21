<?
// web.php
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Models\Test;
use Illuminate\Http\Request;

Route::get('/tests/create', [TestController::class, 'create'])->name('tests.create');
Route::post('/tests/generate', [TestController::class, 'generate'])->name('tests.generate');


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




