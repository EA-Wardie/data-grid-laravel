<?php

namespace Eawardie\DataGrid;

use Closure;
use Eawardie\DataGrid\Definitions\ColumnDefinition;
use Eawardie\DataGrid\Definitions\IconDefinition;
use Eawardie\DataGrid\Definitions\ViewDefinition;
use Eawardie\DataGrid\Models\DataGridModel;
//use Eawardie\DataGrid\Traits\DynamicCompare;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DataGrid
{
    //all data grid properties
    private Builder $query;
    private ?Collection $existingConfig;
    private ?Collection $request;
    private ?string $ref;
    private int $page = 1;
    private int $itemsPerPage = 50;
    private int $totalItems = 0;
    private int $totalPages = 0;
    private array $search = [
        'term' => '',
        'initial' => true,
        'recommendations' => [],
        'queries' => [],
    ];
    private array $sortBy = [];
    private array $filters = [];
    private array $columns = [];
    private array $items = [];
    private array $layouts = [];
    private array $metaData = [];
    private bool $filterWithConfig = false;
    private bool $searchWithSession = false;
    private bool $sortWithSession = false;
    private bool $pageWithSession = false;
    private bool $hyperlinks = false;

    //indicates column types that are accepted as advanced
    private const ADVANCED_COLUMN_TYPES = ['number', 'perc', 'timestamp', 'enum', 'icon'];

    //dynamic comparison trait used to determine icon per items
//    use DynamicCompare;

    //inits data grid
    public function __construct($ref)
    {
        $this->ref = $ref;
        $this->setRequest(collect(json_decode(base64_decode(request('q')), true)));
    }

    //creates an instance of the data grid
    public static function forTable(string $ref): DataGrid
    {
        return new self($ref);
    }

    //sets the query to be used by the data grid
    //when query of type relation is used, use the getQuery() helper function on the original query
    public function forQuery(Builder $query): DataGrid
    {
        $this->setQuery($query);
        $this->handleConfig();

        return $this;
    }

    //returns the query of the data grid
    public function getQuery(): Builder
    {
        return $this->query;
    }

    //sets the query for the data grid
    public function setQuery($query): DataGrid
    {
        $this->query = $query;

        return $this;
    }

    //returns the current request
    public function getRequest(): ?Collection
    {
        return $this->request;
    }

    //sets the current request
    public function setRequest($request = []): DataGrid
    {
        $this->request = $request;

        return $this;
    }

    //returns the current page
    public function getPage(): int
    {
        return $this->page;
    }

    //sets the current page
    public function setPage(): DataGrid
    {
        $this->page = $this->request->get('page', 1);

        return $this;
    }

    //returns the current data grid reference
    public function getReference(): ?string
    {
        return $this->ref;
    }

    //returns the current data grid columns
    //pass true for labels only to get an array of column labels, useful for data export
    public function getColumns(bool $labelsOnly = false): array
    {
        if ($labelsOnly) {
            collect($this->columns)->pluck('label');
        }

        return $this->columns;
    }

    //sets the current data grid columns
    public function setColumns(array $columns): DataGrid
    {
        $this->columns = $columns;

        return $this;
    }

    //switches the filter storage from config (default) to session
    public function filterWithConfig(): DataGrid
    {
        $this->filterWithConfig = true;

        return $this;
    }

    //switches search storage from route parameters (default) to session
    public function searchWithSession(): DataGrid
    {
        $this->searchWithSession = true;

        return $this;
    }

    //switches sort storage from route parameters (default) to session
    public function sortWithSession(): DataGrid
    {
        $this->sortWithSession = true;

        return $this;
    }

    //switches page storage from route parameters (default) to session
    public function pageWithSession(): DataGrid
    {
        $this->pageWithSession = true;

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to add an advanced column
    //includes a list of helper functions to build a column from scratch
    //allows for fine grain column control
    public function addAdvancedColumn(Closure $closure): DataGrid
    {
        $column = $closure(new ColumnDefinition())->toArray();
        $index = count($this->columns);
        $column['index'] = $index;
        $column['originalIndex'] = $index;
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to add a simple column
    //has less functionality but generally less code to use
    public function addColumn(string $value, string $label, string $type, bool $searchable = true, bool $sortable = true): DataGrid
    {
        $index = count($this->columns);
        $basicValueArray = explode('.', $value);
        $basicValue = $basicValueArray[count($basicValueArray) - 1];
        $enumerators = [];

        if ($type === 'enum') {
            $cloned = clone $this->query;
            $enumerators = $cloned->select(DB::raw('DISTINCT ' . $value . ' AS value'))
                ->get()
                ->mapWithKeys(function ($item) {
                    $text = implode(' ', array_map('ucfirst', explode('_', $item->value)));

                    return [$item->value => $text];
                })
                ->toArray();
        }

        $this->column($basicValue, $value, $label, $type, $index, $searchable, $sortable, [], $enumerators);

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to add an icon column
    //icon columns only contain icons
    //can also take the advanced IconDefinition class as a closure for fine grain icon condition control per item
    public function addIconColumn(string $value, string $label, $icon, string $color = 'grey', bool $searchable = true, bool $sortable = true): DataGrid
    {
        $index = count($this->columns);
        $basicValueArray = explode('.', $value);
        $basicValue = $basicValueArray[count($basicValueArray) - 1];
        $iconMap = [];

        if (gettype($icon) === 'string') {
            $iconMap = [[
                'icon' => $icon,
                'value' => null,
                'color' => $color,
                'tooltip' => null,
                'operator' => null,
                'default' => true,
            ]];
        } elseif ($icon instanceof Closure) {
            $iconMap = $icon(new IconDefinition())->toArray();
        }

        $this->column($basicValue, $value, $label, 'icon', $index, $searchable, $sortable, $iconMap);

        return $this;
    }

    public function addFileColumn(string $value, string $label, string $icon = 'mdi-file', string $iconColor = 'grey'): DataGrid
    {
        $index = count($this->columns);
        $this->column($value, $value, $label, 'file', $index);

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to add layouts to the data grid
    //layouts are added with LayoutDefinition class
    public function views(...$layoutDefinitions): DataGrid
    {
        $this->validateLayoutDefinitions($layoutDefinitions);

        $this->layouts = collect($layoutDefinitions)->map(function ($layoutDefinition, $index) {
            $layout = $layoutDefinition(new ViewDefinition())->toArray();
            $id = 'predefined' . '_' . $index;

            return [
                'id' => $id,
                'columns' => $layout['columns'],
                'label' => $layout['label'],
                'current' => $this->existingConfig['currentLayout'] === $id,
            ];
        })->toArray();

        $this->validateLayouts();

        if (count($this->existingConfig['layouts']) > 0) {
            $this->layouts = collect($this->layouts)->concat($this->existingConfig['layouts'])->toArray();
        }

        return $this;
    }

    //switches whether to show hyperlinks for emails or not
    //email hyperlinks will automatically open the default email writer with a draft for that email address
    public function hyperlinks(): DataGrid
    {
        $this->hyperlinks = true;

        return $this;
    }

    /**
     * @return array
     *
     * @throws Exception
     * @throws Throwable
     */
    //function to manage and return final state of the data grid
    //can only be called as the very last function
    public function get(): array
    {
        $this->setFromRequest();
        $this->applyLayout();
        $this->prepareItems();
        $this->setTotals();
        $this->applyPaging();
        $this->setItems();
        $this->prepareMetaData();

        return [
            'items' => $this->items,
            'metaData' => $this->metaData,
        ];
    }

    //sets or gets the database config for the data grid
    //configs are based on the data grid reference and current user auth ID
    private function handleConfig()
    {
        if (DataGridModel::authHasConfiguration($this->ref)) {
            $this->existingConfig = $this->getConfiguration();
        } else {
            $this->existingConfig = $this->setConfiguration();
        }
    }

    //sets data grid options from current page request
    //changes may take effect based on route, session of config settings
    private function setFromRequest()
    {
        $defaultSearch = [
            'term' => '',
            'initial' => true,
            'recommendations' => [],
            'queries' => [],
        ];

        if (!$this->pageWithSession) {
            $this->page = $this->request->get('page', 1);
        } else {
            $this->page = session($this->ref)['page'] ?? 1;
        }

        if (!$this->sortWithSession) {
            $this->sortBy = $this->request->get('sortBy', []);
        } else {
            $this->sortBy = session($this->ref)['sortBy'] ?? [];
        }

        if (!$this->searchWithSession) {
            $this->search = $this->request->get('search', $defaultSearch);
        } else {
            $this->search = session($this->ref)['search'] ?? $defaultSearch;
        }

        if (!$this->filterWithConfig) {
            $this->filters = session($this->ref)['filters'] ?? [];
        } else {
            $this->filters = $this->existingConfig['filters'] ?? [];
        }
    }

    //function to prepare final data grid meta data
    private function prepareMetaData(): void
    {
        $this->metaData = [
            'tableRef' => $this->ref,
            'page' => $this->page,
            'itemsPerPage' => $this->itemsPerPage,
            'totalItems' => $this->totalItems,
            'totalPages' => $this->totalPages,
            'sortBy' => $this->sortBy,
            'filters' => $this->filters,
            'search' => $this->search,
            'columns' => $this->columns,
            'layouts' => $this->layouts,
            'hyperlinks' => $this->hyperlinks,
            'states' => [
                'filter' => $this->filterWithConfig ? 'config' : 'session',
                'search' => $this->searchWithSession ? 'session' : 'route',
                'sort' => $this->sortWithSession ? 'session' : 'route',
                'page' => $this->pageWithSession ? 'session' : 'route',
            ],
        ];
    }

    //applies a selected layout onto the data grid
    //can also be a custom layout created by the user
    private function applyLayout()
    {
        if (isset($this->existingConfig['currentLayout']) && (bool)$this->existingConfig['currentLayout']) {
            $layout = collect($this->layouts)->firstWhere('id', $this->existingConfig['currentLayout']);
            if ((bool)$layout) {
                $this->columns = collect($this->columns)->map(function ($column) use ($layout) {
                    $value = $column['isRaw'] ? $column['value'] : $column['rawValue'];
                    $found = collect($layout['columns'])->firstWhere('value', $value);

                    if ((bool)$found) {
                        $column['hidden'] = false;
                        $column['index'] = $found['order'];
                    } else {
                        $column['hidden'] = true;
                    }

                    return $column;
                })->toArray();
            }
        }
    }

    //returns the current data grid config from the database
    private function getConfiguration(): ?Collection
    {
        if ($this->ref) {
            return collect(DataGridModel::getConfigurationData($this->ref));
        }

        return null;
    }

    //sets the current data grid config in the database
    private function setConfiguration()
    {
        if ($this->ref) {
            $data = [
                'tableRef' => $this->ref,
                'layouts' => [],
                'currentLayout' => null,
                'filters' => [],
            ];

            return DataGridModel::setConfigurationData($this->ref, $data);
        }

        return [];
    }

    /**
     * @throws Exception
     */
    //function to prepare the final items for the data grid
    private function prepareItems(): void
    {
        $this->applySelects();
        $this->applySortBy();
        $this->getSearchRecommendations();
        $this->applySearch();
        $this->applyFilters();
    }

    //sets total item and page counts for paging and front-end display
    private function setTotals()
    {
        $this->totalItems = $this->getCountForPagination();
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
    }

    private function getCountForPagination(): int
    {
        return $this->query->toBase()->getCountForPagination();
    }

    //selects all required values for items to be displayed on the front-end
    private function applySelects()
    {
        collect($this->columns)->each(function ($column) {
            $this->selectValues($column);
            $this->selectAvatar($column);
        });
    }

    //selects specifically the item values
    private function selectValues(array $column)
    {
        $this->query->addSelect(DB::raw($column['rawValue'] . ($column['isRaw'] ? ' AS ' . $column['value'] : '')));

        if (isset($column['rawSubtitle']) && (bool)$column['rawSubtitle']) {
            $this->query->addSelect(DB::raw($column['rawSubtitle']));
        }

        if (isset($column['iconConditionRawValue']) && (bool)$column['iconConditionRawValue']) {
            $this->query->addSelect(DB::raw($column['iconConditionRawValue'] . ' AS ' . $column['iconConditionValue']));
        }
    }

    //sets avatar file details if the advanced column avatar function is used
    private function selectAvatar(array $column)
    {
        if (isset($column['avatar'])) {
            $this->query->leftJoin('file', $column['avatar'], 'file.fileid');
            $this->query->addSelect('file.thumbnail_key AS file_key');
            $this->query->addSelect('file.disk AS file_disk');
        }
    }

    //applies sort orders based on front-end selections
    private function applySortBy()
    {
        if (count($this->sortBy) > 0) {
            foreach ($this->sortBy as $value => $direction) {
                $this->query->orderBy($value, $direction);
            }
        }
    }

    private function getSearchRecommendations()
    {
        $this->search['recommendations'] = [];
        foreach ($this->columns as $column) {
            if (!$column['hidden'] && $column['searchable'] && !$column['isAdvanced'] && isset($this->search['term']) && (bool)$this->search['term']) {
                $value = $column['isRaw'] ? $column['value'] : $column['rawValue'];
                $this->search['recommendations'][] = [
                    'text' => $column['label'] . ' contains <b>"' . $this->search['term'] . '"</b>',
                    'value' => $value,
                    'type' => $column['type'],
                ];
            }
        }
    }

    //applies search queries as selected on the front-end
    //if search is in its initial stage this function provides recommendations
    private function applySearch()
    {
        if (isset($this->search['queries']) && count($this->search['queries']) > 0) {
            $index = 0;
            foreach ($this->search['queries'] as $key => $terms) {
                $column = collect($this->columns)->firstWhere('rawValue', $key);
                if (!$column) {
                    $column = collect($this->columns)->firstWhere('value', $key);
                }

                if ($index === 0) {
                    $clause = $column['isAggregate'] ? 'havingRaw' : 'whereRaw';
                } else {
                    $clause = $column['isAggregate'] ? 'orHavingRaw' : 'orWhereRaw';
                }

                if ($column) {
                    $this->query->where(function ($query) use ($column, $clause, $terms) {
                        foreach ($terms as $term) {
                            $query->$clause($column['rawValue'] . ' LIKE "%' . strtolower($term) . '%"');
                        }
                    });
                }
                $index++;
            }
        }

        if (!$this->search['initial']) {
            $this->search['term'] = '';
            $this->search['recommendations'] = [];
        }

        $this->prepareMetaData();
    }

    //applies the selected filters from the front-end
    //filters are only applies when advanced column exist
    private function applyFilters()
    {
        if (count($this->filters) > 0) {
            foreach ($this->filters as $key => $filter) {
                if (count($filter) > 0) {
                    $clause = 'where';
                    $identifier = str_replace('_icon', '', $key);
                    $operator = $filter['operator'] === '===' ? '=' : $filter['operator'];
                    $column = collect($this->columns)->firstWhere('rawValue', $identifier);

                    if (isset($column['isAggregate']) && $column['isAggregate']) {
                        $identifier = $column['value'];
                        $clause = 'having';
                    }

                    $this->query->$clause($identifier, $operator, $filter['value']);
                }
            }
        }
    }

    //applies final paging from items
    //table uses 50 items by default
    private function applyPaging()
    {
        if ($this->itemsPerPage > 0) {
            $this->query->skip(($this->page - 1) * $this->itemsPerPage)
                ->take($this->queryInstance->limit ?? $this->itemsPerPage);
        }
    }

    /**
     * @throws Exception
     */
    //function for setting final items states
    //handles basic item values, avatars and icon states
    private function setItems()
    {
        $items = $this->query->get()->toArray();
        $enumColumns = collect($this->columns)->where('type', '=', 'enum')->toArray();
        $avatarColumns = collect($this->columns)->where('avatar', '!=', null)->toArray();
        $iconColumns = collect($this->columns)->where('type', 'icon')->toArray();
        $columnsWithIcons = collect($this->columns)->where('iconConditionValue', '!=', null)->toArray();
        $iconColumns = collect($iconColumns)->merge($columnsWithIcons)->toArray();

        $hasEnumColumns = count($enumColumns) > 0;
        $hasAvatarColumns = count($avatarColumns) > 0;
        $hasIconColumns = count($iconColumns) > 0;
        $modifiedItems = [];

        foreach ($items as $item) {
            if ($hasAvatarColumns) {
                $item['avatar_url'] = $this->generateAvatarUrl($item);
                unset($item['file_key']);
                unset($item['file_disk']);
            }

            if ($hasIconColumns) {
                $icon = $this->getIcon($item, $iconColumns);
                $item = collect($item)->merge($icon)->toArray();
            }

            if ($hasEnumColumns) {
                foreach ($enumColumns as $enumColumn) {
                    $item[$enumColumn['value']] = $enumColumn['enumerators'][$item[$enumColumn['value']]];
                }
            }

            $modifiedItems[] = $item;
        }

        $this->items = $modifiedItems;
    }

    //generates avatar URLs based on previously selected avatar values
    private function generateAvatarUrl($item): ?string
    {
        if (isset($item['file_key']) && (bool)$item['file_key']) {
            return Storage::disk($item['file_disk'])
                ->temporaryUrl($item['file_key'], Carbon::now()->addMinutes(config('filesystems.validity')));
        }

        return null;
    }

    /**
     * @throws Exception
     */
    //gets icon for item based on columns of type icon
    private function getIcon(array $item, array $columns = []): array
    {
        foreach ($columns as $column) {
            if (isset($column['iconConditionValue']) && $column['iconConditionValue']) {
                return [$column['iconConditionValue'] . '_icon' => $this->getIconFromCondition($item[$column['iconConditionValue']], $column['iconMap'])];
            } else {
                return [$column['value'] . '_icon' => $this->getIconFromCondition($item[$column['value']], $column['iconMap'])];
            }
        }

        return [];
    }

    /**
     * @throws Exception
     */
    //returns icon set based on the conditions as specified by the IconDefinition class
    private function getIconFromCondition(?string $value, array $icons): array
    {
        $index = collect($icons)->search(function ($icon) use ($value) {
            return !$icon['default'] && $this->is($value, $icon['operator'], $icon['value']);
        });

        if ($index === false) {
            $index = collect($icons)->search(function ($icon) {
                return $icon['default'];
            });
        }

        return collect($icons[$index])->only(['icon', 'color', 'tooltip'])->toArray();
    }

    //add one column to the total columns of the data grid
    private function column(string $value, string $rawValue, string $label, string $type, int $index = 0, bool $searchable = false, bool $sortable = false, array $iconMap = [], array $enumerators = [])
    {
        $this->columns[] = [
            'value' => $value,
            'rawValue' => $rawValue,
            'label' => $label,
            'type' => $type,
            'index' => $index,
            'originalIndex' => $index,
            'hidden' => false,
            'searchable' => $searchable,
            'sortable' => $sortable,
            'isAggregate' => false,
            'isRaw' => false,
            'isAdvanced' => in_array($type, self::ADVANCED_COLUMN_TYPES),
            'iconMap' => $iconMap,
            'enumerators' => $enumerators,
            'timestampFormat' => 'D MMMM YYYY',
        ];
    }

    /**
     * @throws Throwable
     */
    //validates layout definition to ensure they are of closure instances
    private function validateLayoutDefinitions($definitions)
    {
        foreach ($definitions as $definition) {
            throw_if(!$definition instanceof Closure, 'Layouts must be of type Closure. Use function(LayoutDefinition $layout) instead.');
        }
    }

    /**
     * @throws Throwable
     */
    //validates all passed layouts from the LayoutDefinition class
    //primarily checks whether layouts use column that do not exist on the data grid
    private function validateLayouts()
    {
        throw_if(count($this->layouts) === 0, 'When using layouts() there should be at least one layout specified.');

        $moreThanOneDefault = collect($this->layouts)->where('default', true)->count() > 1;
        throw_if($moreThanOneDefault, 'Only one layout can be set as the default layout.');

        $layoutColumns = collect($this->layouts)->pluck('columns')->flatten(1)->toArray();
        foreach ($layoutColumns as $layoutColumn) {
            $found = collect($this->columns)->firstWhere('value', $layoutColumn['value']) !== null;
            if (!$found) {
                $found = collect($this->columns)->firstWhere('rawValue', $layoutColumn['value']) !== null;
            }

            throw_if(!$found, 'Layout with value "' . $layoutColumn['value'] . '" does not have a corresponding column. Please ensure each layout column has a specified table column.');
        }
    }
}
