<?php

use App\Models\Website;
use App\Http\Controllers\CrawlController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\UrlScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/website', function (Request $request) {
    if ($request->user()->tokenCan('read') 
    && $request->user()->tokenCan('internal')) {
        // TODO: Make this return the latest website
        return response()->json(Website::find(1)->toJson());
    }

    return response()->json(['error' => 'Unauthenticated.'], 401);
});

// Called by the workers to update the state of a crawl
Route::middleware('validCrawlToken')->post('/crawl/update', [CrawlController::class, 'update'])->name('api.crawl.update');

// Called by the workers to add a url or urls
Route::middleware('validCrawlToken')->post('/sites/{id}/urls', [UrlController::class, 'addUrls'])->name('api.site.urls.add');

// Called by the workers to update the state of a scan
Route::middleware('validScanToken')->post('/url_scan/update', [UrlScanController::class, 'update'])->name('api.scan.update');

// Called by the workers to complete a scan
Route::middleware('validScanToken')->post('/url_scan/results', [UrlScanController::class, 'results'])->name('api.scan.results');