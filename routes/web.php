<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\UrlController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');


Route::middleware(['auth:sanctum', 'verified'])
    ->get('/sites', [WebsiteController::class, 'index'])
    ->name('sites.list');

Route::inertia('/sites/add', 'Sites/Create');

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/sites/{id}', [WebsiteController::class, 'show'])
    ->name('sites.show');    

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/sites/settings', [WebsiteController::class, 'settings'])
    ->name('sites.settings');

Route::middleware(['auth:sanctum', 'verified'])
    ->post('/sites/add', [WebsiteController::class, 'add'])
    ->name('sites.add');

Route::middleware(['auth:sanctum', 'verified'])
    ->post('/sites/{id}/scan', [WebsiteController::class, 'scan'])
    ->name('sites.scan');

Route::middleware(['auth:sanctum', 'verified'])
    ->delete('/sites/delete/{id}', [WebsiteController::class, 'delete'])
    ->name('sites.delete');

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/sites/{id}/urls', [UrlController::class, 'index'])
    ->name('sites.urls.list');

Route::middleware(['auth:sanctum', 'verified'])
    ->post('/sites/{id}/urls/add', [UrlController::class, 'add'])
    ->name('sites.urls.add');

Route::middleware(['auth:sanctum', 'verified'])
    ->post('/sites/{id}/urls/scan', [UrlController::class, 'crawl'])
    ->name('sites.urls.crawl');

Route::middleware(['auth:sanctum', 'verified'])
    ->delete('/sites/{id}/urls/{url_id}/delete', [UrlController::class, 'delete'])
    ->name('sites.urls.delete');