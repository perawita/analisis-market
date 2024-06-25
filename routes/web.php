<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapingController;

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
    return view('Index');
})->name('Index');

Route::get('/Search', [ScrapingController::class, '_HandlePencarian'])->name('cari-data');
Route::get('/quote/{Symbol}', [ScrapingController::class, 'summary'])->name('summary');
Route::get('/quote/{Symbol}/news', [ScrapingController::class, 'news'])->name('news');
Route::get('/quote/{Symbol}/chart', [ScrapingController::class, 'chart'])->name('chart');
Route::get('/quote/{Symbol}/community', [ScrapingController::class, 'community'])->name('community');
Route::get('/quote/{Symbol}/history', [ScrapingController::class, 'history'])->name('history');
Route::get('/quote/{Symbol}/options', [ScrapingController::class, 'options'])->name('options');
Route::get('/quote/{Symbol}/components', [ScrapingController::class, 'components'])->name('components');
Route::get('/quote/{Symbol}/profile', [ScrapingController::class, 'profile'])->name('profile');
Route::get('/quote/{Symbol}/key-statistics', [ScrapingController::class, 'statistics'])->name('statistics');
Route::get('/quote/{Symbol}/financials', [ScrapingController::class, 'financials'])->name('financials');
Route::get('/quote/{Symbol}/analysis', [ScrapingController::class, 'analysis'])->name('analysis');
Route::get('/quote/{Symbol}/holders', [ScrapingController::class, 'holders'])->name('holders');
Route::get('/quote/{Symbol}/sustainability', [ScrapingController::class, 'sustainability'])->name('sustainability');


