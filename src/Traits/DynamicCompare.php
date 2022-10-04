<?php

namespace Eawardie\DataGrid\Traits;

use Exception;

trait DynamicCompare
{
    private array $operatorToMethodTranslation = [
        '===' => 'equal',
        '!=' => 'notEqual',
        '>' => 'greaterThan',
        '<' => 'lessThan',
        '>=' => 'greaterThanOrEqual',
        '<=' => 'lessThanOrEqual',
    ];

    /**
     * @throws Exception
     */
    protected function is(?string $valueA, ?string $operation, ?string $valueB)
    {
        if ($method = $this->operatorToMethodTranslation[$operation]) {
            return $this->$method($valueA, $valueB);
        }

        throw new Exception('Unknown Dynamic Operator "'.$operation.'"');
    }

    private function equal($valueA, $valueB): bool
    {
        return $valueA === $valueB;
    }

    private function notEqual($valueA, $valueB): bool
    {
        return $valueA != $valueB;
    }

    private function greaterThan($valueA, $valueB): bool
    {
        return $valueA > $valueB;
    }

    private function lessThan($valueA, $valueB): bool
    {
        return $valueA < $valueB;
    }

    private function greaterThanOrEqual($valueA, $valueB): bool
    {
        return $valueA >= $valueB;
    }

    private function lessThanOrEqual($valueA, $valueB): bool
    {
        return $valueA <= $valueB;
    }
}
