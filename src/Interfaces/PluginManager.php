<?php

declare(strict_types=1);

namespace Estasi\Filter\Interfaces;

/**
 * Interface PluginManager
 *
 * @package Estasi\Filter\Interfaces
 */
interface PluginManager extends \Estasi\PluginManager\Interfaces\PluginManager
{
    /**
     * Returns an object of the filter class
     *
     * When using this method, you can't store the created object in the cache
     * This method MUST always return a newly created object
     *
     * @param string                       $name
     * @param iterable<string, mixed>|null $options
     *
     * @return \Estasi\Filter\Interfaces\Filter
     * @throws \Estasi\PluginManager\Exception\NotFoundException
     * @throws \Estasi\PluginManager\Exception\ContainerException
     * @api
     */
    public function getFilter(string $name, iterable $options = null): Filter;
}
