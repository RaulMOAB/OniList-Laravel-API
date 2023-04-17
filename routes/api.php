<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AuthController;

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

Route::get('/user', function (Request $request) {
    return 'Hello World';
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::get('/', [MediaController::class, 'popularAnime']);
Route::get('/anime', [MediaController::class, 'popularAnime']);
Route::get('/manga', [MediaController::class, 'popularManga']);
Route::get('/trending/anime', [MediaController::class, 'trendingAnime']);
Route::get('/trending/manga', [MediaController::class, 'trendingManga']);
