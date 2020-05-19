<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class SnakeCase
 *
 * Converts any string to a snake case
 * For example, snake case -> snake_case or camelCase -> camel_case
 * the string will be in lowercase
 *
 * @package Estasi\Filter
 */
final class SnakeCase extends Abstracts\Filter
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
        return new Lowercase();
    }
}
