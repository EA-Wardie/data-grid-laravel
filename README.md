# Data Grid Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eawardie/data-grid-laravel.svg?style=flat-square)](https://packagist.org/packages/eawardie/data-grid-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/eawardie/data-grid-laravel.svg?style=flat-square)](https://packagist.org/packages/eawardie/data-grid-laravel)

### Data grid package used to build advanced grids with a matching front-end package.

#### Allows for searching, filtering, paging & some other advanced features.

## Installation

You can install the package via composer:

```bash
composer require eawardie/data-grid-laravel
```

Please also note that a companion front-end [package](https://github.com/EA-Wardie/data-grid-vuetify) is required to use this package to it's fullest:

```bash
npm install data-grid-vuetify
```

## Info

The primary responsibility of this package is to provide an easily understandable development path to create simple or advanced data grids.
Although this package can be used in a standalone fashion it is recommended to be used with its companion front-end
package [data-grid-vue](https://github.com/EA-Wardie/data-grid-vue).
This front-end package provides all the necessary tools for rendering data grids on your front-end including making use of all the simple and advanced features as
provided by this package.

## Usage

Package usage can be split up into multiple sections. All will be discussed below:

### Initial setup

Some initial setup is required. Most notably running the included migration of this package.

#### Migration

```bash
php artisan migrate
```

### Creating a data grid

Data grids can be created by using DataGrid facade in any php class as required. The following is a simple example of a data grid.
An in-depth explanation follows:

```php
$query = User::query();
$data = DataGrid::forQuery($query)
   ->addColumn('name', 'Name', 'text')
   ->get();
```

The example above shows the simplest form op setting up a data grid. Data grids consist of mainly 2 aspects:
- Query used for the data itself
- Various properties to build the data grid

#### Query
This query will be used to gather and display any data that is actually required in the data grid.
The query can be any laravel based query using Models, the DB facade or relationships.
It is however important to note that the expected type is `Illuminate\Database\Eloquent\Builder`.
This is prevalent in the example when using the `query()` helper on the `User` model.
Also note that any relationship access should be handled with `join()` and `leftJoin()` manually.

#### Properties
Lastly we look at data grid properties. The `DataGrid` facade make a large amount of properties available to the developer for use.
In the above example the `addColumn()` property is used. As its name suggests, it adds a column to the data grid.
There are however many properties to use. From adding a simple column, to adding advanced columns, specifying data grid settings to setting up pre-defined data grid views.
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

#### `addColumn(value, label, type, searchable, sortable)`
The `value` parameter indicates the DB column value. 
Important note, when using joins in your query. 
Values must be prefixes with table names.

The `label` parameter indicates the actual label used for the column on the front-end.

The `type`  parameter indicate the type the column takes on.
Types are used by the data grid to apply search terms and filters.
There is only a set amount of filters available.
these will be listed and explained at a later stage.

The `searchable` parameter indicates whether to make this column available for searching.

The `sortable` parameter indicates whether to make this column available for sorting.

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
The `addIconColumn()`, as the name suggests, can be used to add a column to the data grid that only displays an icon.
This function also take a series of parameters. These are covered below:

`addIconColumn(value, label, icon, color, searchable, sortable)`

The `value` parameter indicates the DB column value.
Important note, when using joins in your query.
Values must be prefixes with table names.

The `label` parameter indicates the actual label used for the column on the front-end.

The `icon` parameter take either a string or callback function depending on the column requirements.
When passing an icon as a string. That icon will be used in all cases.
When passing a callback function it must be of type `IconDefinition`.
`IconDefinition` is used for fine grain control over what icon displays per column per value based on a series of conditions.
An example is listed below:
```php
$query = User::query();
$data = DataGrid::forQuery($query)
   ->addIconColumn('user.mobile_verified_at', 'Verified', function (IconDefinition $icon) {
       return $icon->condition('account-lock', '=', null, 'error', 'No Password')
          ->condition('account-check', '!=', null, 'success', 'Has Password');
})
```

### `views()`
The `views()` function is used to pre-define data grid layouts for users to select from.
Pre-defined views are passed to this function with callback functions. Each callback is of type `ViewDefinition`.
`ViewDefinition` is used to build a data grid view. An example is listed below.
```php
$query = User::query()->leftJoin('client', 'user.clientid', '=', 'client.clientid');
$data = DataGrid::forQuery($query)
   ->addColumn('user.name', 'Name', 'text')
   ->addColumn('client.name', 'Client', 'text')
   ->views(function (ViewDefinition $view) {
       return $view->column('client.name')
          ->label('Clients');
})
```

### `hyperlinks()`
The `hyperlinks()` function sets whether email addresses are indicated as links on the front-end.

### `load()`
The `load()` function can be used to load model relationships dynamically as required.
`load()` take either comma seperated parameters for each relationship or an `array` of relationship names.
Loaded data will be appended to row items.

### `addSelect()`
The `addSelect()` function, as its name suggests, simply adds a select to the final data gris item list.
This is primary used id extra data is required but not automatically select via added columns.

### `mapRow()`
The `mapRow()` function is used to mutate the current pages row items.
This function takes a callback function which receives each row item on the current page.
It should be noted that any mutations made here are evaluated for every page item (**50** by default), and thus more complicated mutations can drastically decrease performance.
The below example adds a `has_mobile` property to the final items list:
```php
$query = User::query();
$data = DataGrid::forQuery($query)
   ->addSelect('mobile')
   ->addColumn('name', 'Name', 'text')
   ->mapRow(function ($row) {
      return collect($row)
         ->put('has_mobile', !!$row['mobile'])
         ->toArray();
})
```

### `get()`
The `get()` function is used to return final `DataGrid` data array. If passing data to the front-end this function is required.
This function is also always called last on the `DataGrid` instance.

## Data grid definitions
Multiple definitions exists to be used with the `DataGrid` facade. All current available definitions are covered below.

### ColumnDefinition
The `ColumnDefintion` class is used to define advanced columns for your data grid.
The `ColumnDefinition` instance can take the following property functions:

#### `avatar(identifier, preview)`
The `avatar()` function is used to add an avatar to your advanced column.
It takes a file identifier for e.g. `fileid` as well as a boolean for avatar preview.
If your query contains joins the identifier should be prefixed with the DB column name.
The preview option simply enables users preview the avatar in a larger image on the front-end by clicking on it.

#### `icon()`
The `icon()` function is used to pass an icon to your advanced column. 
This function works exactly the same as the `addIconColumn()` we covered earlier.

#### `value()`
The `value()` function is used to pass the DB column value used to display the actual value of the column.
This function is similar to the `value` parameter on the `addColumn()` function.
It should also be noted that when using raw statements the `value()` function is used for that statement's alias.

#### `rawValue()`
The `rawValue()` function is used when you want to pass raw SQL/MySQL statements if advanced data retrieval is required.
It is important to note that when using `rawValue()` function it is required to also use the `value()` function to pass an alias to that raw statement.
Thus, you **DO NOT** add an `AS` section to your raw statement.

#### `subtitle()`
The `subtitle()` function is used to add a subtitle value to your column.
The value parameter is the DB column identifier for the value you would like to display.
This value is also used as an alias when using raw statements.

#### `rawSubtitle()`
The `rawSubtitle()` function is used to pass raw SQL/MySQL statements for subtitles.
It is important to note that when using `rawSubtitle()` function it is required to also use the `subtitle()` function to pass an alias to that raw statement.
Thus, you **DO NOT** add an `AS` section to your raw statement.

#### `type()`
The `type()` function is used to specify the column type of the column itself.
Different column types apply different styles, search parameters and filters to that column.
Available types are:
- `text` - Simple text format - `uses search`
- `email` - Email formatting and opens email client on click - `uses search`
- `number` - Number formatting - `uses filters`
- `perc` - Draws a progress bar on the front-end - `uses filters`
- `timestamp` - Formats value as timestamp - `uses filters`
- `enum` - Formats value as an enum - `uses filters`
- `icon` - Adds an icon to column - `uses filters`

#### `subtitleType()`
The `subtitleType()` function is used to specify the main column subtitle type.
Subtitle types are only used to apply formatting on the front-end.
**Later installments will add searching and filters for subtitles.**

#### `label()`
The `label()` function is used to add a label for the column.
Label is displayed on the front-end.

#### `enumerators()`
The `enumerators()` function is used to add enum `key -> value` pairs for available enum values for that column.
This function is only used when the column `type` is `enum`.
Columns of type `enum` auto-detect enum values when used. 
This function can however override these values if they render incorrectly.

#### `hidden()`
The `hidden()` function is used to mark that column as hidden.
Hidden columns do not render on the front-end. 
This function is not recommended as the hidden property is usually set through the `views()` function.
If a column should be hidden by default it's better to just not include that column.

#### `searchable()`
The `searchable()` function is used to indicate whether the column can be searched.
When applying this option to a column of a searchable type that value will be added to search recommendations.

#### `timestampFormat()`
The `timestampFormat()` column can be used to override the data grid's default timestamp format.
This function is only recommended when the column is of type `timestamp`.
An example is: `D MMM YYYY`.

### IconDefinition
The `IconDefinition` class is used to define advanced icons for columns.
This class can be used with the `addIconColumn()` or the `addAdvancedColumn()` functions.
The `IconDefinition` instance can take the following properties:

#### `condition()`
The `condition()` function is used to add conditions for specific icons on a column.
The function takes 5 possible parameters:
- `icon` - Icon to be displayed if condition evaluates to true
- `operator` - Comparison operator - available operators: `===, !=, <, >, <=, >=`
- `value` - The value to be sued in the comparison
- `color` - The color of the icon to be used
- `tooltip` - If set the icon will display a tooltip on hover

#### `defualt()`
The `defualt()` function is used to specify a default icon to display if none of the conditions evaluate to `true`.
Also takes a color as a second parameter. this defaults to `grey`.

**Other available definitions are `EnumDefinition` and `FileDefinition`.
these are however still under construction and planned for later release.**

## Conclusion
This then concludes the documentation for data-grid-laravel. For question please contact [EA-wardie](https://github.com/EA-wardie).

## Credits

- [EA-wardie](https://github.com/EA-wardie)
- [stian-scoltz](https://github.com/stian-scholtz)
- [ianrothmann](https://github.com/ianrothmann)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
