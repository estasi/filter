<?php

declare(strict_types=1);

namespace Estasi\Filter\Abstracts;

use Estasi\Utility\{
    Assert,
    Interfaces\Charset,
    Interfaces\PhpExt
};

/**
 * Class LowercaseUppercase
 *
 * @package Estasi\Filter\Abstracts
 */
abstract class LowercaseUppercase extends Filter
{
    // names of constructor parameters to create via the factory
    public const OPT_ENCODING = 'encoding';
    // default values for constructor parameters
    public const DEFAULT_ENCODING = Charset::UTF_8;

    protected string $encoding;

    /**
     * LowercaseUppercase constructor.
     *
     * @param string $encoding The encoding parameter is the character encoding. If it is omitted, the UTF-8
     *                         character encoding value will be used.
     */
    public function __construct(string $encoding = self::DEFAULT_ENCODING)
    {
        Assert::extensionLoaded(PhpExt::MBSTRING);
        $this->encoding = $encoding;
    }
}
