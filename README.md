# Estasi Filter

This is a set of frequently used, unchangeable filters that are required.
Provides a simple data filtering chain that allows you to apply multiple filters in the specified order.

## Installation
To install with a composer:
```
composer require estasi/filter
```

## Requirements
- PHP 7.4 or newer
- ext-mbstring
- ext-intl
- [Data Structures](https://github.com/php-ds/polyfill): 
    `composer require php-ds/php-ds`
    <br><small><i>Polyfill is installed with the estasi/filter package.</i></small>


## Usage

### Basic usage
```php
<?php

declare(strict_types=1);

use Estasi\Filter\CamelCase;

$filter = new CamelCase();

echo $filter->filter('camel case'); // camelCase
// or 
echo $filter('camel_case'); // camelCase
```
### Chain
Here are two filters tasks in the chain: explicitly (by class declaration) and via the factory (array):
```php
<?php

declare(strict_types=1);

use Estasi\Filter\{Chain,Trim};

$string = ' camel case string   ';

$chain = new Chain(Chain::DEFAULT_PLUGIN_MANAGER, 'trim', 'camelCase');

// the same thing
$chain = new Chain();
$chain = $chain->attach(new Trim())
               ->attach([Chain::FILTER_NAME => 'camelCase']); // or attach('camelCase')

echo $chain->filter($string); // "camelCaseString"
```

## License
All contents of this package are licensed under the [BSD-3-Clause license](https://github.com/estasi/filter/blob/master/LICENSE.md).