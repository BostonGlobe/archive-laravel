<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('frontpage');
});

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::get('/{path}', [ArticleController::class, 'show'])->where('path', '[a-zA-Z0-9\/\-_\.]+');
