<?php
use App\Http\Controllers\api\ArticleController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;


Route::get('/', function () {
    return redirect()->to('https://www.anjaniwahyudi.com');
 });

Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/storage/images/{filename}', [ImageController::class, 'show']);

Route::get('/logingoogle', [GoogleController::class, 'redirectToGoogle']);
Route::get('/logingooglecallback', [GoogleController::class, 'handleGoogleCallback']);
