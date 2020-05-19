<?php

declare(strict_types=1);

namespace Estasi\Filter;

use function mb_strtolower;

/**
 * Class Lowercase
 *
 * @package Estasi\Filter
 */
final class Lowercase extends Abstracts\LowercaseUppercase
{
    use Traits\IsString;

    /**
     * Make a string lowercase
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

        return mb_strtolower($value, $this->encoding);
    }
}
