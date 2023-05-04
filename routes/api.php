<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MailController;

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

Route::get('/user/{id}', [AccountController::class, 'show']);

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(AccountController::class)->group(function () {
    Route::post('/account/delete/{id}', 'destroy');
});

Route::get('/', [MediaController::class, 'popularAnime']);
Route::get('/anime', [MediaController::class, 'popularAnime']);
Route::get('/manga', [MediaController::class, 'popularManga']);
Route::get('/trending/anime', [MediaController::class, 'trendingAnime']);
Route::get('/trending/manga', [MediaController::class, 'trendingManga']);
Route::get('/upcoming/anime', [MediaController::class, 'upcomingAnime']);
Route::get('/upcoming/manga', [MediaController::class, 'upcomingManga']);
Route::get('/anime/{id}', [MediaController::class, 'show']);
Route::post('/search/anime', [MediaController::class, 'filteredMedia']);


Route::get('/send/{email}', [MailController::class, 'index']);
// Route::post('/verifyMail',[MailController::class, 'verifyMail']);
Route::get('/account/{id}', [AccountController::class, 'show']);


Route::get('/library/{username}', [LibraryController::class, 'libraryInfo']);
Route::get('/library/{username}/animelist', [LibraryController::class, 'animeList']);
Route::get('/library/{username}/mangalist', [LibraryController::class, 'mangaList']);

Route::get('/status/{user_id}/{media_id}', [LibraryController::class, 'getMediaStatus']);
Route::post('/status', [LibraryController::class, 'setMediaStatus']);
Route::post('/media/data', [LibraryController::class, 'insertOrUpdateMediaData']);
Route::post('/media/favorite', [LibraryController::class, 'insertOrUpdateFavorite']);
Route::delete('/media/delete/{media_id}', [LibraryController::class, 'deleteMedia']);
