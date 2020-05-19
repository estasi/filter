<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class CamelCase
 *
 * Converts any string to a camel case
 * For example, camel case -> camelCase
 *
 * @package Estasi\Filter
 */
final class CamelCase extends Abstracts\Filter
{
    use Traits\CamelCasePascalCase;

    /**
     * @inheritDoc
     */
    protected function getCallbackFirstLetter(): callable
    {
        return new Lowercase();
    }
}
