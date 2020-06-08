<?php

declare(strict_types=1);

namespace Estasi\Filter;

use Ds\Vector;
use Estasi\Filter\Interfaces\Filter;
use Estasi\Utility\Interfaces\VariableType;

use function explode;
use function gettype;
use function implode;
use function is_iterable;
use function is_numeric;
use function is_string;

/**
 * Class Each
 *
 * @package Estasi\Filter
 */
final class Each extends Abstracts\Filter
{
    // names of constructor parameters to create via the factory
    public const OPT_FILTER    = 'filter';
    public const OPT_DELIMITER = 'delimiter';
    // default values for constructor parameters
    public const WITHOUT_DELIMITER = null;

    private Filter  $filter;
    private ?string $delimiter;

    /**
     * Each constructor.
     *
     * @param \Estasi\Filter\Interfaces\Filter $filter
     * @param string|null                      $delimiter
     */
    public function __construct(Filter $filter, ?string $delimiter = null)
    {
        $this->filter    = $filter;
        $this->delimiter = $delimiter;
    }

    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        $type     = null;
        $rawValue = $value;
        if (is_numeric($value)) {
            $type  = gettype($value);
            $value = [$value];
        }
        if (is_string($value)) {
            $type  = gettype($value);
            $value = $this->delimiter ? explode($this->delimiter, $value) : [$value];
        }
        if (is_iterable($value)) {
            $type  ??= gettype($value);
            $value = new Vector($value);
        } else {
            return $rawValue;
        }

        $value = $value->map($this->filter);

        switch ($type) {
            case VariableType::INTEGER:
                return (int)$value;
            case VariableType::DOUBLE:
                return (float)$value;
            case VariableType::STRING:
                return implode($this->delimiter ?? '', $value->toArray());
            default:
                return $value->toArray();
        }
    }
}
