<?php

declare(strict_types=1);

namespace Estasi\Filter\Abstracts;

use Ds\Vector;

use function count;
use function is_iterable;

/**
 * Class BlackOrWhiteList
 *
 * @package Estasi\Filter\Abstracts
 */
abstract class BlackOrWhiteList extends Filter
{
    // names of constructor parameters to create via the factory
    public const OPT_LIST = 'list';

    private Vector $list;

    /**
     * BlackOrWhiteList constructor.
     *
     * @param mixed ...$list
     */
    public function __construct(...$list)
    {
        if (count($list) === 1 && is_iterable($list[0])) {
            $list = $list[0];
        }

        return new Vector($list);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function filter($value)
    {
        return $this->list->contains($value);
    }
}
