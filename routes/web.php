<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\Route;

// Halaman Utama Storefront Grosiria
Route::get('/', function () {
    // Jika file prototipe UI HTML sudah ditaruh di docs/ui-prototype/ui_grosiria.html, tampilkan langsung
    $prototypePath = base_path('docs/ui-prototype/ui_grosiria.html');
    if (file_exists($prototypePath)) {
        return response()->file($prototypePath);
    }

    return view('welcome');
})->name('home');

// Rute Fallback 'login' agar middleware Authenticate Laravel tidak pernah error "Route [login] not defined"
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');
