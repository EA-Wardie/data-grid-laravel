<?php

namespace Eawardie\DataGrid\Facades;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method \Eawardie\DataGrid\DataGrid forQuery(Builder $query)
 * @method \Eawardie\DataGrid\DataGrid getQuery()
 * @method \Eawardie\DataGrid\DataGrid setQuery(Builder $query)
 * @method \Eawardie\DataGrid\DataGrid getRequest()
 * @method \Eawardie\DataGrid\DataGrid setRequest(array $request)
 * @method \Eawardie\DataGrid\DataGrid getPage()
 * @method \Eawardie\DataGrid\DataGrid setPage(int $page)
 * @method \Eawardie\DataGrid\DataGrid getReference()
 * @method \Eawardie\DataGrid\DataGrid getColumns()
 * @method \Eawardie\DataGrid\DataGrid setColumns(array $columns)
 * @method \Eawardie\DataGrid\DataGrid filterWithConfig()
 * @method \Eawardie\DataGrid\DataGrid searchWithSession()
 * @method \Eawardie\DataGrid\DataGrid sortWithSession()
 * @method \Eawardie\DataGrid\DataGrid pageWithSession()
 * @method \Eawardie\DataGrid\DataGrid addAdvancedColumn(Closure $closure)
 * @method \Eawardie\DataGrid\DataGrid addColumn(string $value, string $label, string $type, bool $searchable, bool $sortable)
 * @method \Eawardie\DataGrid\DataGrid addIconColumn(string $value, string $label, $icon, string $color, bool $searchable, bool $sortable)
 * @method \Eawardie\DataGrid\DataGrid views($layoutDefinitions)
 * @method \Eawardie\DataGrid\DataGrid hyperlinks()
 * @method \Eawardie\DataGrid\DataGrid get()
 *
 * @see \Eawardie\DataGrid\DataGrid
 */
class DataGrid extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'data-grid';
    }
}
