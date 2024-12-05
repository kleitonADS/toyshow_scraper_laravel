<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\ProductController;


Route::get('/', [ScraperController::class, 'index'])->name('index'); // Página inicial (index) // Página inicial
Route::post('/scraper/start', [ScraperController::class, 'start'])->name('scraper.start');  // Iniciar o scraper
Route::post('/stop-scraper', [ScraperController::class, 'stopScraper'])->name('stop-scraper');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
