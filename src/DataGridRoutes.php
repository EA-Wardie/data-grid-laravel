<?php

namespace Eawardie\DataGrid;

use Illuminate\Support\Facades\Route;

class DataGridRoutes
{
    public static function get(): void
    {
        Route::group([], function () {
            Route::post('/datagrid/{ref}/layout', 'DataGrid\DataGridController@layout')
                ->name('datagrid.layout');
            Route::post('/datagrid/{ref}/view', 'DataGrid\DataGridController@view')
                ->name('datagrid.view');
            Route::post('/datagrid/{ref}/filters', 'DataGrid\DataGridController@filters')
                ->name('datagrid.filters');
            Route::post('/datagrid/{ref}/search', 'DataGrid\DataGridController@search')
                ->name('datagrid.search');
            Route::post('/datagrid/{ref}/sort', 'DataGrid\DataGridController@sort')
                ->name('datagrid.sort');
            Route::post('/datagrid/{ref}/page', 'DataGrid\DataGridController@page')
                ->name('datagrid.page');
        });
    }
}
