# Data Grid Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eawardie/data-grid.svg?style=flat-square)](https://packagist.org/packages/eawardie/data-grid)
[![Total Downloads](https://img.shields.io/packagist/dt/eawardie/data-grid.svg?style=flat-square)](https://packagist.org/packages/eawardie/data-grid)

### Data table package used to build advanced tables with a matching front-end package.

#### Allows for searching, filtering, paging & some other advanced features.

## Installation

You can install the package via composer:

```bash
composer require eawardie/data-grid-laravel
```

Please also note that a companion front-end [package](https://github.com/EA-Wardie/data-grid-vue) is required to use this package to it's fullest:

```bash
npm install data-grid-vue --save
```

## Info

The primary responsibility of this package is to provide an easily understandable development path to create simple or advanced data tables.
Although this package can be used in a standalone fashion it is recommended to be used with its companion front-end
package [data-grid-vue](https://github.com/EA-Wardie/data-grid-vue).
This front-end package provides all the necessary tools for rendering tables on your front-end including making use of all the simple and advanced features as
provided by this package.

## Usage

Package usage can be split up into multiple sections. All will be discussed below:

### Initial setup

Some initial setup is required. Most notably running the included migration of this package. As well as registering the routes included in this package.

#### Migration

```bash
php artisan migrate
```

#### Registering routes

To register the included package routes, simply add the following line of code to your app's main route file:

```php
DataGridRoutes::get();
```

### Creating a data grid

Data grids or tables can be created by using DataGrid facade in any php class as required. The following is a simple example of a data grid.
An in-depth explanation follows:

```php
$query = User::query();
$data = DataGrid::forTable('cfd57041-f48f-3134-b55d-b06cc5e92d5b')
    ->forQuery($query)
    ->addColumn('name', 'Name', 'text')
    ->get();
```

The example above shows the simples from op setting up a data table. Data tables consist of mainly 3 aspects:
- Unique table reference
- Query used for the data itself
- Various properties to build the data grid

#### Table reference
Starting out we are required to provide a unique reference to the table bing built. This can be any string or series of numbers.
The most important part is simply that this reference is unique to all other data grids in your app.
In this example we used a uuid generated string.

#### Query
After giving the data grid a reference we also have to pass a data query. 
This query will be used to gather and display any data that is actually required in the table.
The query can be any laravel based query using Models, the DB facade or relationships.
It is however important to note that the expected type is `Illuminate\Database\Eloquent\Builder`.
This is prevalent in the example when using the `query()` helper on the `User` model.

#### Properties
Lastly we look at data grid properties. The `DataGrid` facade make a large amount of properties available to the developer for use.
In the above example the `addColumn()` property is used. As its name suggests, it adds a column to the data grid.
There are however many properties to use. From adding a simple column, to adding advanced columns, specifying table settings to setting up pre-defined data grid views.
All these will be covered and thoroughly explained.

### Finalizing your data grid
After specifying any and all properties as required, the `get()` function can be called on the data grid instance to get a neatly formatted keyed array with the data grid items and meta data.
This array can be passed to your front-end for usage with data-grid-vue to render a data grid.
However, if used in a standalone fashion this array can also be used in any way as required by the developer.
The "items" key contains the actual items to be rendered in the data grid. The "metaData" key contains all required settings passed to the data grid.
See an example below:
```php
array:2 [▼
  "items" => array:50 [▶]
  "metaData" => array:12 [▶]
]
```

## Data grid properties
The following section will explain every property available on the data grid instance.

### `forTable()`
As mentioned previously the `forTable()` function is called on the static version of the DataGrid facade and used to pass a unique reference.

### `forQuery()`
Also, as mentioned above the `forQuery()` function is used to pass the actual data query to the data grid.

### `getQuery()`
The `getQuery()` function can be used to get an instance of the current data grid query.

### `setQuery()`
The `setQuery()` function can be used to set the query of the data grid.

### `getRquest()`
The `getRequest()` function can be used to get the current page request.

### `setRquest()`
The `setRequest()` function can be used to set the current request.

### `getPage()`
The `getPage()` function can be used to get the current page number.

### `setPage()`
The `setPage()` function can be used to set the current page number.

### `getreference()`
The `getreference()` function can be used to get the data grid reference.

### `setColumns()`
The `setColumns()` function can be used to set the data grid columns. 
Note though, that this method should be avoided if possible.
Data grid columns should be set using the built in `addColumn()`, `addAdvancedColumn()`, `addIconColumn()` and `addFileColumn()` functions.

### `filterWithConfig()`
The `filterWithConfig()` function is used to apply the setting that forces the data grid to use the persistent configuration to apply and consume filters. 
This allows data grid filters to persist even after closing the app.

### `searchWithSession()`
The `searchWithSession()` function is used to apply the setting that forces the data grid to use session storage when searching instead of route parameters.

### `sortWithSession()`
The `sortWithSession()` function is used to apply the setting that forces the data grid to use session storage when sorting instead of route parameters.

### `pageWithSession()`
The `pageWithSession()` function is used to apply the setting that forces the data grid to use session storage when paging instead of route parameters.

### `addColumn()`
The `addColumn()` function is used to add a column to the data grid.
This function takes a few parameters to set up the column correctly.
These are covered below:

#### `addColumn(value, label, type)`
The `value` parameter indicates the DB column value. 
Important note, when using joins in your query. 
Values must be prefixes with table names.

The `label` parameter indicates the actual label used for the column on the front-end.

The `type`  parameter indicate the type the column takes on.
Types are used by the data grid to apply search terms and filters.
There is only a set amount of filters available.
these will be listed and explained at a later stage.

### `addAdvancedColumn()`
The `addAdvancedColumn()` function is also used to add a column to the data grid.
This function however takes a callback function of type `ColumnDefinition`.
`ColumnDefinition` can be used for fine grain control over columns and for applying advanced functionality.
Definitions will be covered at a later stage in full detail.
An example is listed below:
```php
->addAdvancedColumn(function (ColumnDefinition $column) {
    return $column->value('user.name')
        ->type('text')
        ->label('User')
        ->subtitle('client.name')
        ->subtitleType('text');
})
```

### `addIconColumn()`
The `addiconcolumn()`, as the name suggests, can be used to add a column to the data grid that only displays

## Credits

- [EA-wardie](https://github.com/EA-wardie)
- [stian-scoltz](https://github.com/stian-scholtz)
- [ianrothmann](https://github.com/ianrothmann)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
