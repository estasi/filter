<?php

declare(strict_types=1);

namespace Estasi\Filter\Abstracts;

use Estasi\Filter\{
    Interfaces,
    Traits\Filtration
};
use Estasi\Utility\{
    Traits\Disable__call,
    Traits\Disable__callStatic,
    Traits\Disable__set
};

/**
 * Class Filter
 *
 * @package Estasi\Filter\Abstracts
 */
abstract class Filter implements Interfaces\Filter
{
    use Filtration;
    use Disable__call;
    use Disable__callStatic;
    use Disable__set;
}
