<?php

namespace Eawardie\DataGrid\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Eawardie\DataGrid\DataGrid
 */
class DataGrid extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'data-grid';
    }
}
