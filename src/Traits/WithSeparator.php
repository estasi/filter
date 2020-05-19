<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use Estasi\Filter\Lowercase;

/**
 * Trait WithSeparator
 *
 * @package Estasi\Filter\Traits
 */
trait WithSeparator
{
    use FilterCallbackArray;

    /**
     * Returns the word separator in a string
     *
     * @return string
     */
    abstract protected function getSeparator(): string;

    /**
     * Returns the function for converting the entire string to uppercase or lowercase
     *
     * @return callable
     */
    abstract protected function getCallbackCase(): callable;

    /**
     * @inheritDoc
     */
    protected function getPatternsAndCallbacks(): array
    {
        $lowercase = new Lowercase();

        return [
            '`[[:punct:]\s]+$`Su'   => fn(array $match): string => '',
            '`^(\p{Lu}?\p{Ll}+)`Su' => fn(array $match): string => $lowercase($match[1]),
            '`(\p{Lu}\p{Ll}+)`Su'   => fn(array $match): string => $this->getSeparator() . $lowercase($match[1]),
            '`[[:punct:]\s]+`Su'    => fn(array $match): string => $this->getSeparator(),
            '`(.+)`Su'              => fn(array $match): string => $this->getCallbackCase()($match[1]),
        ];
    }
}
