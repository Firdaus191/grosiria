<?php

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    // Endpoint Pengujian UTS T08 (/api/v1/users)
    Route::get('/users', [UserController::class, 'index'])->name('api.v1.users.index');
});
