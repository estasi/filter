<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class Trim
 *
 * @package Estasi\Filter\String
 */
final class Trim extends Abstracts\Trim
{
    use Traits\IsString;

    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        if (false === $this->isString($value)) {
            return $value;
        }

        return \trim($value, $this->characterMask);
    }
}
