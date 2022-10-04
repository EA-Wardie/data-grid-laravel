<?php

namespace Eawardie\DataGrid\Definitions;

use Closure;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class IconDefinition implements Arrayable
{
    //icon properties
    private array $iconMap = [];

    //all available operators to be used with icon conditions
    private const AVAILABLE_OPERATORS = ['===', '!=', '<', '>', '<=', '>='];

    public function __construct()
    {
    }

    /**
     * @throws Throwable
     */
    //function to specify a condition for an icon
    public function condition(string $icon, string $operator, ?string $value, string $color = 'grey', string $tooltip = null): IconDefinition
    {
        $map = [
            'icon' => $icon,
            'value' => $value,
            'color' => $color,
            'tooltip' => $tooltip,
            'operator' => $operator === '=' || $operator === '==' ? '===' : $operator,
            'default' => false,
        ];

        $this->validateCondition($map, count($this->iconMap) + 1);
        $this->iconMap[] = $map;

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify if an icon it accepted as the default if no condition are true on an item
    public function default(string $icon, string $color = 'grey'): IconDefinition
    {
        $this->iconMap[] = [
            'icon' => $icon,
            'value' => null,
            'color' => $color,
            'tooltip' => null,
            'operator' => null,
            'default' => true,
        ];

        return $this;
    }

    /**
     * @throws Throwable
     */
    //validates all icon conditions
    private function validateCondition(array $map, $index)
    {
        throw_if(!$map['icon'], 'Icon is required on condition index ' . $index);
        throw_if(!$map['operator'], 'Operator is required on condition index ' . $index);
    }

    /**
     * @throws Throwable
     */
    //validates all icon values
    private function validateValues()
    {
        throw_if(count($this->iconMap) === 0, 'At least one condition required. Or use default() to specify a default icon.');
    }

    /**
     * @throws Throwable
     */
    //validates all condition operators to ensure only allowed operators are used
    private function validateOperators()
    {
        foreach ($this->iconMap as $iconMap) {
            if (!!$iconMap['operator'] && !in_array($iconMap['operator'], self::AVAILABLE_OPERATORS)) {
                if ($iconMap['operator'] === '<>') {
                    throw new Exception('The ' . $iconMap['operator'] . " operator is not allowed. Did you mean '!='?");
                } else {
                    throw new Exception('The ' . $iconMap['operator'] . ' operator is not allowed. Allowed operators include [' . implode(', ', self::AVAILABLE_OPERATORS) . ']');
                }
            }
        }
    }

    /**
     * @throws Throwable
     */
    //returns icon map with all its properties
    public function toArray(): array
    {
        $this->validateValues();
        $this->validateOperators();

        return $this->iconMap;
    }
}
