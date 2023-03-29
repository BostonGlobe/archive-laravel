<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

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


Route::get('/{path}', function ($path) {
    return $path;
    $filePath = storage_path('resources/articles/' . $path . 'index.html');
    dd($filePath);

    if (!file_exists($filePath)) {
        return '<h1>404</h1>';
    }

    $html = file_get_contents($filePath);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    
    // Remove header and footer
    $scripts = $dom->getElementsByTagName('script');
    foreach ($scripts as $script) {
        $script->parentNode->removeChild($script);
    }
    // $footer = $dom->getElementsByTagName('footer')->item(0);
    // $dom->removeChild($header);
    // $footer->parentNode->removeChild($footer);

    // Append new header and footer
    // $newHeader = $dom->createElement('header', 'New Header');
    // $newFooter = $dom->createElement('footer', 'New Footer');
    // $dom->documentElement->insertBefore($newHeader, $dom->documentElement->firstChild);
    // $dom->documentElement->appendChild($newFooter);

    $updatedHtml = $dom->saveHTML();

    // Cache the updated HTML markup
    Cache::put($path, $updatedHtml, 60);

    return $updatedHtml;
});
