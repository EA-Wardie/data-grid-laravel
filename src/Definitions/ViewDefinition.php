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

    public function __construct()
    {
    }

    //function to specify a view column
    //column must exist in dat grid
    public function column(string $value, int $order = 0): ViewDefinition
    {
        $this->columns[] = [
            'value' => $value,
            'order' => $order,
            'hidden' => true,
        ];

        return $this;
    }

    /**
     * @throws Exception
     */
    //function to specify view label
    public function label(string $label): ViewDefinition
    {
        $this->label = $label;

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
        throw_if(! $this->label, 'A view label is required. Use label() to add one.');
    }
}
