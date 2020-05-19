<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class ConstCase
 *
 * Similarly SnakeCase only all in uppercase
 * For example, snake case -> SNAKE_CASE or camelCase -> CAMEL_CASE
 *
 * @package Estasi\Filter
 */
final class ConstCase extends Abstracts\Filter
{
    use Traits\WithSeparator;

    /**
     * @inheritDoc
     */
    protected function getSeparator(): string
    {
        return '_';
    }

    /**
     * @inheritDoc
     */
    protected function getCallbackCase(): callable
    {
        return new Uppercase();
    }
}
