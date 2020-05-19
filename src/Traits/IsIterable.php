<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use Estasi\Utility\ArrayUtils;
use Traversable;

use function is_array;
use function is_object;
use function method_exists;

/**
 * Trait IsArray
 *
 * @package Estasi\Filter\Traits
 */
trait IsIterable
{
    /**
     * Finds whether a variable is an array, iterable or an object that has the toArray method
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function isIterable(&$value): bool
    {
        if (is_object($value) && method_exists($value, 'toArray')) {
            $value = $value->toArray();
        } elseif ($value instanceof Traversable) {
            $value = ArrayUtils::iteratorToArray($value);
        }

        return is_array($value);
    }
}
