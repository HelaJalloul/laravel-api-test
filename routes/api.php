<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
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

Route::post('/', [LoginController::class, 'authenticate']);
Route::get('/profiles', [ProfileController::class, 'getProfiles']);

Route::middleware('connected')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfiles']);
    Route::post('/profile', [ProfileController::class, 'setProfile']);
    Route::patch('/profile/{id}', [ProfileController::class, 'updateProfile'])->where(['id' => '[0-9]+']);
    Route::delete('/profile/{id}', [ProfileController::class, 'destroyProfile'])->where(['id' => '[0-9]+']);
});


