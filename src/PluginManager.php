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
}
