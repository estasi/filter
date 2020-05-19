<?php

declare(strict_types=1);

namespace Estasi\Filter\Interfaces;

use Countable;

/**
 * Interface Chain
 *
 * @package Estasi\Filter\Interfaces
 */
interface Chain extends Filter, Countable
{
    public const DEFAULT_PLUGIN_MANAGER = null;

    // Names of attach and prepend parameters for creating an object through the factory
    public const FILTER_NAME     = 'filter';
    public const FILTER_OPTIONS  = 'options';
    public const FILTER_PRIORITY = 'priority';

    /**
     * Attaches the filter to the queue
     *
     * This method MUST be implemented in a way that preserves the chain immutability, and must return an instance that
     * has a modified call chain
     *
     * @param string|iterable<string, mixed>|\Estasi\Filter\Interfaces\Filter $filter
     * @param int                                                             $priority
     *
     * @return $this new instance
     * @api
     */
    public function attach($filter, int $priority = 1): self;

    /**
     * Attaches the filter to the top of the queue
     *
     * This method MUST be implemented in a way that preserves the chain immutability, and must return an instance that
     * has a modified call chain
     *
     * @param string|iterable<string, mixed>|\Estasi\Filter\Interfaces\Filter $filter
     *
     * @return $this new instance
     * @api
     */
    public function prepend($filter): self;

    /**
     * Attaches the entire chain at once. Analogous to the entry in the class constructor.
     *
     * ATTENTION!!! When writing, the chain created in the constructor MUST be cleared.
     *
     * @param iterable<string, mixed>|\Estasi\Filter\Interfaces\Filter[] $filters
     *
     * @internal
     */
    public function putAll(iterable $filters): void;

    /**
     * Chain constructor.
     *
     * @param \Estasi\Filter\Interfaces\PluginManager|null                    $pluginManager
     * @param string|iterable<string, mixed>|\Estasi\Filter\Interfaces\Filter ...$filters
     */
    public function __construct(?PluginManager $pluginManager = self::DEFAULT_PLUGIN_MANAGER, ...$filters);
}
