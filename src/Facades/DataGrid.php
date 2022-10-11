<?php

namespace Eawardie\DataGrid\Facades;

use Closure;
use Eawardie\DataGrid\DataGridService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method DataGridService forQuery(Builder $query)
 * @method DataGridService getQuery()
 * @method DataGridService setQuery(Builder $query)
 * @method DataGridService getRequest()
 * @method DataGridService setRequest(array $request)
 * @method DataGridService getPage()
 * @method DataGridService setPage(int $page)
 * @method DataGridService getReference()
 * @method DataGridService getColumns()
 * @method DataGridService setColumns(array $columns)
 * @method DataGridService filterWithConfig()
 * @method DataGridService searchWithSession()
 * @method DataGridService sortWithSession()
 * @method DataGridService pageWithSession()
 * @method DataGridService addAdvancedColumn(Closure $closure)
 * @method DataGridService addColumn(string $value, string $label, string $type, bool $searchable, bool $sortable)
 * @method DataGridService addIconColumn(string $value, string $label, $icon, string $color, bool $searchable, bool $sortable)
 * @method DataGridService views($layoutDefinitions)
 * @method DataGridService hyperlinks()
 * @method DataGridService get()
 *
 * @see \Eawardie\DataGrid\DataGridService
 */
class DataGrid extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'data-grid';
    }
}
