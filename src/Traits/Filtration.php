<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

/**
 * Trait Filtration
 *
 * @package Estasi\Filter\Traits
 */
trait Filtration
{
    /**
     * @inheritDoc
     */
    public function __invoke($value)
    {
        return $this->filter($value);
    }
}
