<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use Estasi\Filter\{
    Lowercase,
    Uppercase
};

/**
 * Trait CamelCaseOrPascalCase
 *
 * @package Estasi\Filter\Traits
 */
trait CamelCasePascalCase
{
    use FilterCallbackArray;

    /**
     * Returns the function for converting the first character of a string
     *
     * @return callable
     */
    abstract protected function getCallbackFirstLetter(): callable;

    /**
     * @inheritDoc
     */
    protected function getPatternsAndCallbacks(): array
    {
        $lowercase = new Lowercase();
        $uppercase = new Uppercase();

        $wordUpperToLower          = fn(array $match): string => $lowercase($match[0]);
        $firstLetterUpperRestLower = fn(array $match): string => $uppercase($match[2]) . $lowercase($match[3]);
        $firstLetterLower          = fn(array $match): string => $this->getCallbackFirstLetter()($match[1]);

        return [
            '`(\p{Lu}\p{L}*)[[:punct:]\s]+`Su'    => $wordUpperToLower,
            '`([[:punct:]\s]+)(\p{L})(\p{L}*)`Su' => $firstLetterUpperRestLower,
            '`^(\p{L})`Su'                        => $firstLetterLower,
        ];
    }
}
