<?php

declare(strict_types=1);

namespace Estasi\Filter;

use function mb_strtoupper;

/**
 * Class Uppercase
 *
 * @package Estasi\Filter
 */
final class Uppercase extends Abstracts\LowercaseUppercase
{
    use Traits\IsString;

    /**
     * Make a string uppercase
     *
     * @param string|mixed $value
     *
     * @return string|mixed
     */
    public function filter($value)
    {
        if (false === $this->isString($value)) {
            return $value;
        }

        return mb_strtoupper($value, $this->encoding);
    }
}
