<?php

declare(strict_types=1);

namespace Estasi\Filter;

use Closure;
use Ds\Vector;

use function call_user_func_array;

/**
 * Class Callback
 *
 * @package Estasi\Filter
 */
final class Callback extends Abstracts\Filter
{
    // names of constructor parameters to create via the factory
    public const OPT_CALLBACK = 'callback';
    public const OPT_PARAMS   = 'params';
    // default values for constructor parameters
    public const WITHOUT_CALLBACK_PARAMS = null;

    private Closure  $callback;
    private Vector   $params;

    /**
     * Callback constructor.
     *
     * @param callable      $callback The callable to be called.
     * @param iterable|null $params   The parameters to be passed to the callback, as an indexed array
     */
    public function __construct(callable $callback, iterable $params = self::WITHOUT_CALLBACK_PARAMS)
    {
        $this->callback = $callback;
        $this->params   = new Vector($params ?? []);
    }

    /**
     * Call a callback with an array of parameters
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value)
    {
        $this->params->unshift($value);

        return call_user_func_array($this->callback, $this->params->toArray());
    }
}
