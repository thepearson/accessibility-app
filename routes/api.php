<?php

use App\Models\Website;
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


