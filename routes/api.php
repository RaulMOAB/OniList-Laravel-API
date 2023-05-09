<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RelationsController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\PeopleController;

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

Route::middleware(['cors'])->group(function () {
    
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
    Route::get('/anime/top', [MediaController::class, 'topAnime']);
    Route::get('/manga', [MediaController::class, 'popularManga']);
    Route::get('/trending/anime', [MediaController::class, 'trendingAnime']);
    Route::get('/trending/manga', [MediaController::class, 'trendingManga']);
    Route::get('/upcoming/anime', [MediaController::class, 'upcomingAnime']);
    Route::get('/upcoming/manga', [MediaController::class, 'upcomingManga']);
    Route::get('/anime/{id}', [MediaController::class, 'show']);
    Route::post('/search/anime', [MediaController::class, 'filteredMediaAnime']);
    Route::post('/search/manga', [MediaController::class, 'filteredMediaManga']);
    
    
    Route::get('/send/{email}', [MailController::class, 'index']);
    // Route::post('/verifyMail',[MailController::class, 'verifyMail']);
    Route::get('/account/{id}', [AccountController::class, 'show']);
    
    
    Route::get('/library/{username}', [LibraryController::class, 'libraryInfo']);
    Route::get('/library/{username}/animelist', [LibraryController::class, 'animeList']);
    Route::get('/library/{username}/mangalist', [LibraryController::class, 'mangaList']);
    Route::get('/library/{username}/favorites', [LibraryController::class, 'favoritesMedias']);
    Route::get('/library/{username}/stats', [LibraryController::class, 'overviewStats']);
    Route::get('/library/{username}/stats/animelist', [LibraryController::class, 'animelistStats']);
    Route::get('/library/{username}/stats/mangalist', [LibraryController::class, 'mangalistStats']);
    
    Route::get('/status/{user_id}/{media_id}', [LibraryController::class, 'getMediaStatus']);
    Route::post('/status', [LibraryController::class, 'setMediaStatus']);
    Route::post('/media/data', [LibraryController::class, 'insertOrUpdateMediaData']);
    Route::post('/media/favorite', [LibraryController::class, 'insertFavorite']);
    Route::delete('/media/delete/{media_id}', [LibraryController::class, 'deleteMedia']);
    
    //Media related to media
    Route::get('/{media_id}/relations', [RelationsController::class, 'getMediasRelatedTo']);
    //Character
    Route::get('/characters/{character_id}', [CharacterController::class, 'getCharacters']);
    Route::get('/media/characters/{media_id}', [CharacterController::class, 'getCharacterAppearsMedia']);

//Dubbers
// Route::get('/staff/{character_id}', [PeopleController::class, 'peopleDubCharacter']);
// Route::get('/media/staff/{staff_id}', [PeopleController::class, 'peopleDubCharacter']);
});