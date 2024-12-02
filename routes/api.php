<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Prefix untuk Folder
Route::prefix('folders')->group(function () {
    // Folder CRUD
    Route::get('/', [FolderController::class, 'index']); // Ambil semua folder
    Route::get('/{id}', [FolderController::class, 'show']); // Ambil detail folder
    Route::post('/', [FolderController::class, 'store']); // Tambah folder
    Route::put('/{id}', [FolderController::class, 'update']); // Update folder
    Route::delete('/{id}', [FolderController::class, 'destroy']); // Hapus folder

    // Sub-folder dalam folder
    Route::get('/{folderid}/subfolders', [FolderController::class, 'getChildren']); // Ambil sub-folder
    // Route::get('/{folderid}/subfolders', [FolderController::class, 'getChildren']); // Ambil sub-folder
    Route::post('/{folderid}/subfolders', [FolderController::class, 'createSubFolder']); // Tambah sub-folder
});

// Prefix untuk File
Route::prefix('folders/{folderId}/files')->group(function () {
    // File CRUD
    Route::get('/', [FileController::class, 'index']); // Ambil semua file
    Route::get('/{fileid}', [FileController::class, 'show']); // Ambil detail file
    Route::post('/', [FileController::class, 'store']); // Tambah file
    Route::put('/{fileid}', [FileController::class, 'update']); // Update file
    Route::delete('/{fileid}', [FileController::class, 'destroy']); // Hapus file
});