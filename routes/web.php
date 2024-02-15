<?php

use App\Http\Controllers\MediaFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'media-files', 'as' => 'media-files.'], function(){
    Route::get('/', [MediaFileController::class, 'index'])->name('index');
    Route::post('/', [MediaFileController::class, 'store'])->name('store');
    Route::put('/{id}', [MediaFileController::class, 'fileRename'])->name('file-rename');
    Route::delete('/{id}', [MediaFileController::class, 'destroy'])->name('delete');
});
