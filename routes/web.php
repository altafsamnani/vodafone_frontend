<?php

use App\Http\Controllers\Test\KeycloakFrontController;
use App\Http\Controllers\OAuthTokenController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => '/{provider}'], function () {
    Route::get('/auth', [OAuthTokenController::class, 'authenticate']);
    Route::get('/', [OAuthTokenController::class, 'redirect']);
    Route::post('/refresh', [OAuthTokenController::class, 'refresh']);
    Route::post('/revoke', [OAuthTokenController::class, 'revoke']);
    Route::post('/token', [OAuthTokenController::class, 'create']);
    Route::get('/test', [KeycloakFrontController::class, 'index']);
});

Route::get('/', function () {
    return abort(403);
});
