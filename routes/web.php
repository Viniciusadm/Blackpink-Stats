<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home']);

Route::get('/details/{slug}', [SiteController::class, 'details'])
    ->where('slug', '[a-z0-9-]+');

Route::fallback(function () {
    return response()->view('notFound', [], 404);
});
