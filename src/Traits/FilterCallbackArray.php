<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use function preg_replace_callback_array;

/**
 * Trait FilterCallbackArray
 *
 * @package Estasi\Filter\Traits
 */
trait FilterCallbackArray
{
    use IsString;
    use IsIterable;

    /**
     * Returns an associative array that binds regular expression templates (keys) and callback functions (values).
     *
     * @return array<string, callable>
     */
    abstract protected function getPatternsAndCallbacks(): array;

    /**
     * Returns the passed value converted to the required format
     *
     * @param string|string[] $value
     *
     * @return mixed
     */
    public function filter($value)
    {
        if (false === ($this->isString($value) || $this->isIterable($value))) {
            return $value;
        }

        return preg_replace_callback_array($this->getPatternsAndCallbacks(), $value);
    }
}
