<?php

declare(strict_types=1);

namespace Estasi\Filter;

use Estasi\Utility\{
    Traits\ConvertPatternHtml5ToPCRE,
    Traits\ReceivedTypeForException
};
use InvalidArgumentException;

use function array_map;
use function is_array;
use function preg_replace;
use function sprintf;

/**
 * Class PregReplace
 *
 * @package Estasi\Filter
 */
final class PregReplace extends Abstracts\Filter
{
    use ConvertPatternHtml5ToPCRE;
    use ReceivedTypeForException;
    use Traits\IsString;
    use Traits\IsIterable;

    // names of constructor parameters to create via the factory
    public const OPT_PATTERN     = 'pattern';
    public const OPT_REPLACEMENT = 'replacement';
    public const OPT_LIMIT       = 'limit';
    public const OPT_COUNT       = 'count';
    // default values for constructor parameters
    public const UNLIMITED = -1;

    /** @var int|null If specified, this variable will be filled with the number of replacements done */
    public ?int $count;

    /** @var string|string[] The pattern to search for. It can be either a string or an array with strings */
    private $pattern;
    /** @var string|string[] The string or an array with strings to replace */
    private $replacement;
    /** @var int The maximum possible replacements for each pattern in each subject string. Defaults to -1 (no limit) */
    private int $limit;

    /**
     * PregReplace constructor.
     *
     * @param string|string[] $pattern     The pattern in PCRE or html 5 format to search for. It can be either a
     *                                     string or an array with strings
     * @param string|string[] $replacement The string or an array with strings to replace
     * @param int             $limit       The maximum possible replacements for each pattern in each subject string.
     *                                     Defaults to -1 (no limit)
     */
    public function __construct($pattern, $replacement = '', int $limit = self::UNLIMITED)
    {
        $this->assertStringOrArray($pattern, true, self::OPT_PATTERN);
        $this->assertStringOrArray($replacement, true, self::OPT_REPLACEMENT);

        $this->pattern     = is_array($pattern)
            ? array_map(fn(string $item): string => $this->convertPatternHtml5ToPCRE($item), $pattern)
            : $this->convertPatternHtml5ToPCRE($pattern);
        $this->replacement = $replacement;
        $this->limit       = $limit;
    }

    /**
     * Perform a regular expression search and replace
     *
     * @param string|string[]|mixed $value The string or an array with strings to search and replace
     *
     * @return string|string[]|mixed
     */
    public function filter($value)
    {
        if ($this->assertStringOrArray($value)) {
            return $value;
        }

        return preg_replace($this->pattern, $this->replacement, $value, $this->limit, $this->count);
    }

    /**
     * Returns true if the variable type is a string or array, otherwise returns false or throws an exception if the
     * second parameter $throw is set to true
     *
     * @param mixed  $value
     * @param bool   $throw
     * @param string $nameParam
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function assertStringOrArray(&$value, bool $throw = false, string $nameParam = ''): bool
    {
        if (false === ($this->isString($value) || $this->isIterable($value))) {
            if ($throw) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected parameter "%s" a string or string[]! Got: "%s".',
                        $nameParam,
                        $this->getReceivedType($value)
                    )
                );
            }

            return true;
        }

        return false;
    }
}
