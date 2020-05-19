<?php

declare(strict_types=1);

namespace Estasi\Filter\Interfaces;

/**
 * Interface Filter
 *
 * @package Estasi\Filter\Interfaces
 */
interface Filter
{
    /**
     * Filters a variable with a specified filter
     *
     * @param mixed $value
     *
     * @return mixed
     * @api
     */
    public function filter($value);

    /**
     * Filters a variable with a specified filter
     *
     * @param mixed $value
     *
     * @return mixed
     * @api
     */
    public function __invoke($value);
}
