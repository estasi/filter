<?php

declare(strict_types=1);

namespace Estasi\Filter\Abstracts;

/**
 * Class Trim
 *
 * Strip whitespace (or other characters) from the beginning and end of a string
 *
 * @package Estasi\Filter\Abstracts
 */
abstract class Trim extends Filter
{
    // names of constructor parameters to create via the factory
    public const OPT_CHARACTER_MASK = 'characterMask';
    // default values for constructor parameters
    public const DEFAULT_CHARACTER_MASK = ' \t\n\r\0\x0B';

    protected string $characterMask;

    /**
     * Trim constructor.
     *
     * @param string $characterMask Optionally, the stripped characters can also be specified using the character_mask
     *                              parameter. Simply list all characters that you want to be stripped. With .. you can
     *                              specify a range of characters.
     */
    public function __construct(string $characterMask = self::DEFAULT_CHARACTER_MASK)
    {
        $this->characterMask = $characterMask;
    }
}
