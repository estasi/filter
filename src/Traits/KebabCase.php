<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use Estasi\Filter\Lowercase;

/**
 * Trait KebabCase
 *
 * @package Estasi\Filter\Traits
 */
trait KebabCase
{
    use WithSeparator;

    /**
     * @inheritDoc
     */
    protected function getSeparator(): string
    {
        return '-';
    }

    /**
     * @inheritDoc
     */
    protected function getCallbackCase(): callable
    {
        return new Lowercase();
    }
}
