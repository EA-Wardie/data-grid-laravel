<?php

namespace Eawardie\DataGrid\Routes;

use Eawardie\DataGrid\Controllers\DataGridController;
use Illuminate\Support\Facades\Route;

class DataGridRoutes
{
    public static function get(): void
    {
        Route::group([], function () {
            Route::post('/datagrid/{ref}/layout', [DataGridController::class, 'layout'])
                ->name('datagrid.layout');
            Route::post('/datagrid/{ref}/view', [DataGridController::class, 'view'])
                ->name('datagrid.view');
            Route::post('/datagrid/{ref}/filters', [DataGridController::class, 'filters'])
                ->name('datagrid.filters');
            Route::post('/datagrid/{ref}/search', [DataGridController::class, 'search'])
                ->name('datagrid.search');
            Route::post('/datagrid/{ref}/sort', [DataGridController::class, 'sort'])
                ->name('datagrid.sort');
            Route::post('/datagrid/{ref}/page', [DataGridController::class, 'page'])
                ->name('datagrid.page');
        });
    }
}
