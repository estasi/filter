<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class Ltrim
 *
 * @package Estasi\Filter
 */
final class Ltrim extends Abstracts\Trim
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

        return \ltrim($value, $this->characterMask);
    }
}
