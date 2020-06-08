<?php

declare(strict_types=1);

namespace Estasi\Filter\Traits;

use Estasi\Filter\{
    BlackList,
    Callback,
    CamelCase,
    ConstCase,
    Interfaces\Filter,
    KebabCase,
    LispCase,
    Lowercase,
    Ltrim,
    PascalCase,
    PregReplace,
    Rtrim,
    SnakeCase,
    TrainCase,
    Trim,
    UnitFormatter,
    Uppercase,
    Uri,
    WhiteList
};
use Estasi\PluginManager\{
    Interfaces,
    Plugin,
    PluginsList
};

/**
 * Trait PluginManager
 *
 * @package Estasi\Filter\Traits
 */
trait PluginManager
{
    public function getInstanceOf(): ?string
    {
        return Filter::class;
    }

    public function getPlugins(): Interfaces\PluginsList
    {
        return new PluginsList(
            new Plugin(CamelCase::class, ['camelcase', 'camelCase', 'CamelCase', 'camel_case', 'camel-case']),
            new Plugin(ConstCase::class, ['constcase', 'constCase', 'ConstCase', 'const_case', 'const-case']),
            new Plugin(KebabCase::class, ['kebabcase', 'kebabCase', 'KebabCase', 'kebab_case', 'kebab-case']),
            new Plugin(LispCase::class, ['lispcase', 'lispCase', 'LispCase', 'lisp_case', 'lisp-case']),
            new Plugin(PascalCase::class, ['pascalcase', 'pascalCase', 'PascalCase', 'pascal_case', 'pascal-case']),
            new Plugin(SnakeCase::class, ['snakecase', 'snakeCase', 'SnakeCase', 'snake_case', 'snake-case']),
            new Plugin(TrainCase::class, ['traincase', 'trainCase', 'TrainCase', 'train_case', 'train-case']),
            new Plugin(
                Lowercase::class,
                ['lowercase', 'lowerCase', 'Lowercase', 'LowerCase', 'lower_case', 'lower-case', 'strtolower']
            ),
            new Plugin(
                Uppercase::class,
                ['uppercase', 'upperCase', 'UpperCase', 'Uppercase', 'upper_case', 'upper-case', 'strtoupper']
            ),
            new Plugin(BlackList::class, ['blacklist', 'blackList', 'BlackList', 'black_list', 'black-list']),
            new Plugin(WhiteList::class, ['whitelist', 'whiteList', 'WhiteList', 'white_list', 'white-list']),
            new Plugin(Callback::class, ['callback', 'callable', 'Callback', 'Callable']),
            new Plugin(PregReplace::class, ['preg_replace', 'pregReplace', 'PregReplace', 'preg-replace']),
            new Plugin(Ltrim::class, ['ltrim', 'Ltrim', 'LTrim']),
            new Plugin(Rtrim::class, ['rtrim', 'Rtrim', 'RTrim']),
            new Plugin(Trim::class, ['trim', 'Trim']),
            new Plugin(
                Uri::class,
                [
                    'uri',
                    'uriNormalization',
                    'uri_normalization',
                    'uri-normalization',
                    'uriNormalize',
                    'uri_normalize',
                    'uri-normalize',
                ]
            ),
            new Plugin(UnitFormatter::class, ['unit', 'unit_formatter', 'unitFormatter', 'UnitFormatter'])
        );
    }

    /**
     * @inheritDoc
     */
    public function getFilter(string $name, iterable $options = null): Filter
    {
        /** @var \Estasi\Filter\Interfaces\Filter $filter */
        $filter = $this->build($name, $options);

        return $filter;
    }
}
