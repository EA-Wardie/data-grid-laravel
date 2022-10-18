<?php

namespace Eawardie\DataGrid\Definitions;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class ViewDefinition implements Arrayable
{
    //all view properties
    private array $columns = [];
    private string $label = '';
    private array $search = [];
    private array $sortBy = [];
    private array $filters = [];

    public function __construct()
    {
    }

    //function to specify a view column
    //column must exist in dat grid
    public function column(string $value, bool $hidden = false): self
    {
        $index = count($this->columns);
        $this->columns[] = [
            'value' => $value,
            'order' => max($index, 0),
            'hidden' => $hidden,
        ];

        return $this;
    }

    /**
     * @throws Exception
     */
    //function to specify view label
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    //function used to pass pre-defined key -> value pairs for search queries
    public function search(array $search = []): self
    {
        $content = [
            'initial' => false,
            'recommendations' => [],
            'queries' => $search,
        ];

        $this->search = $content;

        return $this;
    }

    //function used to pass pre-defined key -> value pairs for column sorting
    public function sortBy(array $sortBy = []): self
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    //function used to pass pre-defined key -> value pairs for filters
    public function filters(array $filters = []): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @throws Throwable
     */
    //returns view with all its properties
    public function toArray(): array
    {
        $this->validateColumns();
        $this->validateLabel();

        return [
            'columns' => $this->columns,
            'search' => $this->search,
            'sort' => $this->sortBy,
            'filters' => $this->filters,
            'label' => $this->label,
        ];
    }

    /**
     * @throws Throwable
     */
    //validates all view columns
    //also checks for order duplicates
    private function validateColumns()
    {
        throw_if(count($this->columns) === 0, 'At least one column must be specified per view.');
        $withOrderDuplicates = collect($this->columns)->duplicates('order')->isNotEmpty();
        throw_if($withOrderDuplicates, 'More than one column has the same order, this is not allowed.');
    }

    /**
     * @throws Throwable
     */
    //validates view label
    private function validateLabel()
    {
        throw_if(!$this->label, 'A view label is required. Use label() to add one.');
    }
}
