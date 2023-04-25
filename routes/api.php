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

Route::get('/user', function (Request $request) {
    return 'Hello World';
});

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
Route::get('/anime/{id}', [MediaController::class, 'show']);

Route::get('/send/{email}', [MailController::class, 'index']);
// Route::post('/verifyMail',[MailController::class, 'verifyMail']);

Route::get('/library/{id}',[LibraryController::class, 'libraryInfo']);