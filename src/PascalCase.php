<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class PascalCase
 *
 * Converts any string to a pascal case
 * For example, pascal case -> PascalCase
 *
 * @package Estasi\Filter
 */
final class PascalCase extends Abstracts\Filter
{
    use Traits\CamelCasePascalCase;

    /**
     * @inheritDoc
     */
    protected function getCallbackFirstLetter(): callable
    {
        return new Uppercase();
    }
}
