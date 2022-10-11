<?php

use Eawardie\DataGrid\Controllers\DataGridController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/datagrid', 'as' => 'datagrid.', 'middleware' => ['web', 'auth']], function () {
    Route::post('/{ref}/layout', [DataGridController::class, 'layout'])
        ->name('layout');
    Route::post('/{ref}/view', [DataGridController::class, 'view'])
        ->name('view');
    Route::post('/{ref}/filters', [DataGridController::class, 'filters'])
        ->name('filters');
    Route::post('/{ref}/search', [DataGridController::class, 'search'])
        ->name('search');
    Route::post('/{ref}/sort', [DataGridController::class, 'sort'])
        ->name('sort');
    Route::post('/{ref}/page', [DataGridController::class, 'page'])
        ->name('page');
});
