<?php
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {

    return view ('tentang');
});

Route::get('/sapa/{nama}', function ($nama) {

    return "Halo, $nama! Selamat datang di Toko Online.";
});



Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
Route::get('/dashboard', [AdminController::class, 'dashboard'])
    ->name('dashboard');
Route::resource('/products', AdminProductController::class);
    });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::controller(GoogleController::class)->group(function () {
Route::get('/auth/google', 'redirect')
        ->name('auth.google');
Route::get('/auth/google/callback', 'callback')
        ->name('auth.google.callback');
});
