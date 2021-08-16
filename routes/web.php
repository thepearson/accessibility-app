<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\WebsiteController;

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
    ->get('/sites/{id}/urls', [WebsiteController::class, 'urls'])
    ->name('sites.urls');  

// Route::middleware(['auth:sanctum', 'verified'])
//     ->get('/sites/add', [WebsiteController::class, 'add'])
//     ->name('sites.add');

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/sites/settings', [WebsiteController::class, 'settings'])
    ->name('sites.settings');

Route::middleware(['auth:sanctum', 'verified'])
    ->post('/sites/add', [WebsiteController::class, 'add'])
    ->name('sites.add');

Route::middleware(['auth:sanctum', 'verified'])
    ->delete('/sites/delete/{id}', [WebsiteController::class, 'delete'])
    ->name('sites.delete');