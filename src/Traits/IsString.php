<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use function is_object;
use function is_string;
use function method_exists;

/**
 * Trait IsString
 *
 * @package Estasi\Filter\Traits
 */
trait IsString
{
    /**
     * Find whether the type of a variable is string
     * If the variable type is an object and it contains the __toString method, the object is converted to a string
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function isString(&$value): bool
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string)$value;
        }

        return is_string($value);
    }
}
