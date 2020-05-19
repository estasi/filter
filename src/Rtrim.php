<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class Rtrim
 *
 * @package Estasi\Filter
 */
final class Rtrim extends Abstracts\Trim
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

        return \rtrim($value, $this->characterMask);
    }
}
