<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class WhiteList
 *
 * @package Estasi\Filter
 */
final class WhiteList extends Abstracts\BlackOrWhiteList
{
    /**
     * Returns the passed value if present in the white list, otherwise returns null
     *
     * @param mixed $value
     *
     * @return mixed|null
     */
    public function filter($value)
    {
        return parent::filter($value) ? $value : null;
    }
}
