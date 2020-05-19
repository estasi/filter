<?php

declare(strict_types=1);

namespace Estasi\Filter;

use Ds\PriorityQueue;
use Estasi\Filter\{
    Interfaces\Filter,
    Traits\Filtration
};
use Estasi\Utility\{
    ArrayUtils,
    Traits\TopPriority
};
use InvalidArgumentException;

use function is_iterable;
use function is_string;
use function sprintf;

/**
 * Class Chain
 *
 * @package Estasi\Filter
 */
final class Chain implements Interfaces\Chain
{
    use Filtration;
    use TopPriority;

    private PriorityQueue            $filters;
    private Interfaces\PluginManager $pluginManager;

    /**
     * @inheritDoc
     */
    public function __construct(?Interfaces\PluginManager $pluginManager = self::DEFAULT_PLUGIN_MANAGER, ...$filters)
    {
        $this->pluginManager = $pluginManager ?? new PluginManager();
        $this->putAll($filters);
    }

    /**
     * @inheritDoc
     */
    public function attach($filter, int $priority = 1): self
    {
        $new = clone $this;
        $new->processing($filter, $priority);

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function prepend($filter): self
    {
        return $this->attach($filter, $this->getTopPriority());
    }

    /**
     * @inheritDoc
     */
    public function putAll(iterable $filters): void
    {
        $this->filters = new PriorityQueue();
        foreach ($filters as $filter) {
            $this->processing($filter);
        }
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->filters->count();
    }

    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        /** @var Filter $filter */
        foreach ($this->filters->toArray() as $filter) {
            $value = $filter->filter($value);
        }

        return $value;
    }

    public function __clone()
    {
        $filters       = $this->filters->toArray();
        $this->filters = new PriorityQueue();
        foreach ($filters as $item) {
            $this->filters->push($item, 1);
        }
        $this->pluginManager = clone $this->pluginManager;
    }

    private function processing($filter, int $priority = 1): void
    {
        if (is_iterable($filter)) {
            [
                self::FILTER_NAME     => $filter,
                self::FILTER_OPTIONS  => $options,
                self::FILTER_PRIORITY => $priorityTmp,
            ] = ArrayUtils::iteratorToArray($filter);

            $priority = $priorityTmp ?? $priority;
        }

        if (is_string($filter)) {
            $filter = $this->pluginManager->getFilter($filter, $options ?? []);
        }

        if (false === $filter instanceof Filter) {
            throw new InvalidArgumentException(
                sprintf(
                    'The filter is not valid! Expected: "string" - the name of the filter, "array" - containing the filter name or "object" implementing "%s"!',
                    Filter::class
                )
            );
        }

        $this->filters->push($filter, $priority);
        $this->updateTopPriority($priority);
    }
}
