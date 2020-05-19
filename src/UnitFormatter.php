<?php

declare(strict_types=1);

namespace Estasi\Filter;

use NumberFormatter;

use function abs;
use function is_numeric;
use function sprintf;

/**
 * Class UnitFormatter
 *
 * @package Estasi\Filter
 */
final class UnitFormatter extends Abstracts\Filter
{
    // Multiple units
    public const DECA  = 1;
    public const HECTO = 2;
    public const KILO  = 3;
    public const MEGA  = 6;
    public const GIGA  = 9;
    public const TERA  = 12;
    public const PETA  = 15;
    public const EXA   = 18;
    public const ZETTA = 21;
    public const YOTTA = 24;

    // Binary (binary) units
    public const KIBI = 10;
    public const MEBI = 20;
    public const GIBI = 30;
    public const TEBI = 40;
    public const PEBI = 50;
    public const EXBI = 60;
    public const ZEBI = 70;
    public const YOBI = 80;

    // Sub-divisions
    public const DECI  = -1;
    public const CENTI = -2;
    public const MILLI = -3;
    public const MICRO = -6;
    public const NANO  = -9;
    public const PICO  = -12;
    public const FEMTO = -15;
    public const ATTO  = -18;
    public const ZEPTO = -21;
    public const YOCTO = -24;

    public const PREFIX_NAME   = 'name';
    public const PREFIX_SYMBOL = 'symbol';

    private int                $unit;
    private string             $prefix;
    private string             $name;
    private NumberFormatter    $numberFormatter;

    /**
     * UnitFormatter constructor.
     *
     * @param int              $unit            a multiple and a sub-unit that differ from the base unit in a certain
     *                                          integer that is a power of 10, the number of times
     * @param string           $prefix          name of the prefix or symbol of a multiple or subdivided unit before
     *                                          the name or symbol of the unit of measurement
     * @param string           $name            name or designation of the unit of measurement
     * @param \NumberFormatter $numberFormatter
     */
    public function __construct(int $unit, string $prefix, string $name, NumberFormatter $numberFormatter)
    {
        $this->unit            = $unit;
        $this->prefix          = $this->getPrefixNameOrSymbol($unit, $prefix);
        $this->name            = $name;
        $this->numberFormatter = $numberFormatter;
    }

    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        if (false === is_numeric($value)) {
            return $value;
        }

        if ($this->isBinaryUnit()) {
            $value /= 2 ** $this->unit;
        } elseif ($this->isDecimalUnit()) {
            $value /= 10 ** $this->unit;
        } else {
            return $value;
        }

        return sprintf('%s %s%s', $this->numberFormatter->format($value), $this->prefix, $this->name);
    }

    private function getPrefixNameOrSymbol(int $unit, string $prefix): string
    {
        return [
                   self::DECA  => [self::PREFIX_NAME => 'deca', self::PREFIX_SYMBOL => 'da'],
                   self::HECTO => [self::PREFIX_NAME => 'hecto', self::PREFIX_SYMBOL => 'h'],
                   self::KILO  => [self::PREFIX_NAME => 'kilo', self::PREFIX_SYMBOL => 'k'],
                   self::MEGA  => [self::PREFIX_NAME => 'mega', self::PREFIX_SYMBOL => 'M'],
                   self::GIGA  => [self::PREFIX_NAME => 'giga', self::PREFIX_SYMBOL => 'G'],
                   self::TERA  => [self::PREFIX_NAME => 'tera', self::PREFIX_SYMBOL => 'T'],
                   self::PETA  => [self::PREFIX_NAME => 'peta', self::PREFIX_SYMBOL => 'P'],
                   self::EXA   => [self::PREFIX_NAME => 'exa', self::PREFIX_SYMBOL => 'E'],
                   self::ZETTA => [self::PREFIX_NAME => 'zetta', self::PREFIX_SYMBOL => 'Z'],
                   self::YOTTA => [self::PREFIX_NAME => 'yotta', self::PREFIX_SYMBOL => 'Y'],
                   self::KIBI  => [self::PREFIX_NAME => 'kibi', self::PREFIX_SYMBOL => 'Ki'],
                   self::MEBI  => [self::PREFIX_NAME => 'mebi', self::PREFIX_SYMBOL => 'Mi'],
                   self::GIBI  => [self::PREFIX_NAME => 'gigi', self::PREFIX_SYMBOL => 'Gi'],
                   self::TEBI  => [self::PREFIX_NAME => 'tebi', self::PREFIX_SYMBOL => 'Ti'],
                   self::PEBI  => [self::PREFIX_NAME => 'pebi', self::PREFIX_SYMBOL => 'Pi'],
                   self::EXBI  => [self::PREFIX_NAME => 'exbi', self::PREFIX_SYMBOL => 'Ei'],
                   self::ZEBI  => [self::PREFIX_NAME => 'zebi', self::PREFIX_SYMBOL => 'Zi'],
                   self::YOBI  => [self::PREFIX_NAME => 'yobi', self::PREFIX_SYMBOL => 'Yi'],
                   self::DECI  => [self::PREFIX_NAME => 'deci', self::PREFIX_SYMBOL => 'd'],
                   self::CENTI => [self::PREFIX_NAME => 'centi', self::PREFIX_SYMBOL => 'c'],
                   self::MILLI => [self::PREFIX_NAME => 'milli', self::PREFIX_SYMBOL => 'm'],
                   self::MICRO => [self::PREFIX_NAME => 'micro', self::PREFIX_SYMBOL => 'µ'],
                   self::NANO  => [self::PREFIX_NAME => 'nano', self::PREFIX_SYMBOL => 'n'],
                   self::PICO  => [self::PREFIX_NAME => 'pico', self::PREFIX_SYMBOL => 'p'],
                   self::FEMTO => [self::PREFIX_NAME => 'femto', self::PREFIX_SYMBOL => 'f'],
                   self::ATTO  => [self::PREFIX_NAME => 'atto', self::PREFIX_SYMBOL => 'a'],
                   self::ZEPTO => [self::PREFIX_NAME => 'zepto', self::PREFIX_SYMBOL => 'z'],
                   self::YOCTO => [self::PREFIX_NAME => 'yocto', self::PREFIX_SYMBOL => 'y'],
               ][$unit][$prefix];
    }

    private function isBinaryUnit()
    {
        return 0 === $this->unit % 10 || 0 === $this->unit;
    }

    private function isDecimalUnit()
    {
        return 0 === $this->unit % 3 || in_array(abs($this->unit), [1, 2]);
    }
}
