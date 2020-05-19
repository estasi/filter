<?php

declare(strict_types=1);

namespace Estasi\Filter;

/**
 * Class LispCase
 *
 * Converts any string to a lisp case
 * For example, lisp case -> lisp-case or camelCase -> camel-case
 * the string will be in lowercase
 *
 * @package Estasi\Filter
 */
final class LispCase extends Abstracts\Filter
{
    use Traits\KebabCase;
}
