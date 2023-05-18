<?php

use App\Http\Controllers\LibraryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RelationsController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\WorksInController;

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


Route::get('/storage/profile/{filename}', function ($filename) {

    $path = storage_path('app/public/profile/' . $filename);
    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

Route::get('/storage/banner/{filename}', function ($filename) {

    $path = storage_path('app/public/banner/' . $filename);
    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

Route::get('/verify-token', function () {
    return response()->json(['message' => 'Token is valid.']);
})->middleware('auth:api');

Route::middleware(['cors'])->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
    });

    Route::controller(AccountController::class)->group(function () {
        Route::get('/user/{id}', 'show');
        Route::post('/update/image', 'updateProfileImage');    
        Route::post('/update/description','updateDescription');
        Route::post('/update/username', 'updateUsername');
        Route::post('/update/email', 'updateEmail');
        Route::post('/update/password', 'updatePassword');
        Route::post('/account/delete/{id}', 'destroy');
    });
    
    //anime
    Route::get('/', [MediaController::class, 'popularAnime']);
    Route::get('/anime', [MediaController::class, 'popularAnime']);
    Route::get('/anime/popular', [MediaController::class, 'popularAnime']);
    Route::get('/anime/top', [MediaController::class, 'topAnime']);
    Route::get('/anime/trending', [MediaController::class, 'trendingAnime']);
    Route::get('/anime/upcoming', [MediaController::class, 'upcomingAnime']);
    Route::get('/anime/this-season', [MediaController::class, 'thisSeasonAnime']);
    Route::get('/anime/movie', [MediaController::class, 'topMovieAnime']);
    Route::get('/anime/{id}', [MediaController::class, 'show']);
    Route::get('/media/{id}', [MediaController::class, 'show']);
    Route::post('/search/anime', [MediaController::class, 'filteredMediaAnime']);

    //manga
    Route::get('/manga', [MediaController::class, 'popularManga']);
    Route::get('/manga/top', [MediaController::class, 'topManga']);
    Route::get('/manga/trending', [MediaController::class, 'trendingManga']);
    Route::get('/manga/manhwa', [MediaController::class, 'manhwaManga']);
    Route::get('/manga/popular', [MediaController::class, 'popularManga']);
    Route::post('/search/manga', [MediaController::class, 'filteredMediaManga']);
   

    Route::get('/send/{email}', [MailController::class, 'index']);
    Route::post('/forgot-password', [MailController::class, 'forgotPassword']);
    Route::post('/renew-password', [MailController::class, 'renewPassword']);
    Route::post('/send/registered-user-code', [MailController::class, 'send']);
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
    Route::get('/characters/appears/{character_id}', [CharacterController::class, 'getCharacterAppearsIn']);
    Route::get('/media/characters/{media_id}', [CharacterController::class, 'getCharacterAppearsMedia']);

    //Staff
    Route::get('/staff/{id}', [PeopleController::class, 'getStaffPerson']);
    Route::get('/{media_id}/staff', [WorksInController::class, 'getStaff']);
});

Route::get('/media/{id}/users', [MediaController::class, 'countUsersHasMedia']);

