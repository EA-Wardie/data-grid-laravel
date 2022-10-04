<?php

namespace Eawardie\DataGrid\Definitions;

use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class EnumDefinition implements Arrayable
{
    //all enum properties
    private array $enumMap = [];

    public function __construct()
    {
    }

    //function to add an enum item
    public function addItem(string $value, string $text): EnumDefinition
    {
        $this->enumMap[] = [$value => $text];

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to add multiple enum item at a time
    //is validates to ensure item are key value pairs
    public function addItems(array $items): EnumDefinition
    {
        $this->enumMap = $items;
        $this->validateItems();

        return $this;
    }

    //returns enum map and all its properties
    public function toArray(): array
    {
        return $this->enumMap;
    }

    /**
     * @throws Throwable
     */
    //validates items when using addItems function
    private function validateItems()
    {
        throw_if(count($this->enumMap) === 0, 'Items passed as enumerators cannot be empty.');
    }
}
