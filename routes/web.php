<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/tentang', function () {
      return view('tentang');
});
Route::get('/sapa/{nama}', function ($nama) {
 return "Halo, $nama! Selamat datang di Toko Online.";
});
