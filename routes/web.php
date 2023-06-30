<?php

use Illuminate\Support\Facades\Route;
use App\Models\Article;

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
    return view('welcome');
});

Route::get('/{path}', function ($path) {
    $result = Article::fetchFormattedHtmlFile($path);

    if ($result === false) {
        abort(404);
    }

    return $result;
})->where('path', '[a-zA-Z0-9\/\-_\.]+');

Route::get('/search', 'SearchController@search')->name('search');
