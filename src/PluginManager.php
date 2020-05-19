<?php

declare(strict_types=1);

namespace Estasi\Filter;

use Estasi\PluginManager\Abstracts;

/**
 * Class PluginManager
 *
 * @package Estasi\Filter
 */
final class PluginManager extends Abstracts\PluginManager implements Interfaces\PluginManager
{
    use Traits\PluginManager;

    /**
     * @inheritDoc
     */
    public function getFilter(string $name, iterable $options = null): Interfaces\Filter
    {
        /** @var \Estasi\Filter\Interfaces\Filter $filter */
        $filter = $this->build($name, $options);

        return $filter;
    }
}
