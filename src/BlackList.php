<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class BlackList
 *
 * @package Estasi\Filter
 */
final class BlackList extends Abstracts\BlackOrWhiteList
{
    /**
     * Returns the passed value if it is not in the black list, otherwise returns null
     *
     * @param mixed $value
     *
     * @return mixed|null
     */
    public function filter($value)
    {
        return parent::filter($value) ? null : $value;
    }
}
