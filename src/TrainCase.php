<?php

declare(strict_types=1);

namespace Estasi\Filter;

use function preg_replace_callback;

/**
 * Class TrainCase
 *
 * Converts any string to a train case
 * For example, train case -> Train-Case or camelCase -> Camel-Case
 *
 * @package Estasi\Filter
 */
final class TrainCase extends Abstracts\Filter
{
    use Traits\IsString;
    use Traits\IsIterable;

    /**
     * @param string|string[]|mixed $value
     *
     * @return string|string[]|mixed|null
     */
    public function filter($value)
    {
        if (false === ($this->isString($value) || $this->isIterable($value))) {
            return $value;
        }

        $uppercase = new Uppercase();

        return preg_replace_callback(
            '`^(\p{Ll})|(\x2D\p{Ll})`Su',
            fn(array $match): string => $uppercase($match[0]),
            (new LispCase())($value)
        );
    }
}
