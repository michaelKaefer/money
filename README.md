# money
A package for representing, calculating and formatting money units.

[![Total Downloads](https://poser.pugx.org/unit/money/downloads)](//packagist.org/packages/unit/money)
[![Version](https://poser.pugx.org/unit/money/v)](//packagist.org/packages/unit/money)
[![Build Status](https://travis-ci.com/michaelKaefer/money.svg?branch=main)](https://travis-ci.com/michaelKaefer/money)
[![Coverage Status](https://coveralls.io/repos/github/michaelKaefer/money/badge.svg?branch=main)](https://coveralls.io/github/michaelKaefer/money?branch=main)
[![Type Coverage](https://shepherd.dev/github/michaelKaefer/money/coverage.svg)](https://shepherd.dev/github/michaelKaefer/money)

## Installation
```console
user@machine:~$ composer require unit/money
```

## Usage
```php
use Unit\Money\Model\Price;
use Unit\Money\Model\FractionalPrice;
use Litipk\BigNumbers\Decimal;

\Locale::setDefault('en');

// Prices with an integer amount.
// The integer represents an amount in the smallest unit of the given currency.
$price = new Price(1999, 'EUR');

// Prices with a fractional amount.
// For some use cases prices must have a higher precision than the smallest currency unit allows.
$price = new FractionalPrice(Decimal::fromString('234.3464002'), 'EUR');
```

#### Formatting
For formatting prices the package `twig/intl-extra` is used. The `$attributes` array and the 
`$locale` are documented with Twig's `format_currency` filter 
[here](https://twig.symfony.com/doc/3.x/filters/format_currency.html).
```php
use Unit\Money\Model\Price;
use Unit\Money\Formatter\PriceFormatter;
use Twig\Extra\Intl\IntlExtension;

\Locale::setDefault('en');

$formatter = new PriceFormatter(new IntlExtension());
$price = new Price(1999, 'EUR');

// Format
$formatter->format($price); // "€19.99"

// Cut at precision
$formatter->format($price);                          // "€19.99"
$formatter->format($price, ['fraction_digit' => 2]); // "€19.99"
$formatter->format($price, ['fraction_digit' => 1]); // "€20.0"
$formatter->format($price, ['fraction_digit' => 0]); // "€20"
```

The only extra option added by this package is the `template` option:
```php
use Unit\Money\Model\Price;
use Unit\Money\Formatter\PriceFormatter;
use Twig\Extra\Intl\IntlExtension;

// Custom formats can be defined by configuring the formatter ...
$formatter = new PriceFormatter(new IntlExtension(), '%price% %code%');

// ... or by using the "template" option.
$formatter->format(new Price(1999, 'EUR'), ['template' => '%price% %name%']);
```
The available placeholders for the template option are:
- `%price%` is for example: "19,99"
- `%code%` is for example: "EUR"
- `%name%` is for example: "Euro"
- `%symbol%` is for example: "€"


Using different locales:
```php
use Unit\Money\Model\Price;
use Unit\Money\Formatter\PriceFormatter;
use Twig\Extra\Intl\IntlExtension;

\Locale::setDefault('en');

$formatter = new PriceFormatter(new IntlExtension());
$price = new Price(100000, 'INR');

$formatter->format($price, [
    'template' => '%symbol%%price% %code% (%name%)',
], 'en_US'); // "₹1,000.00 INR (Indian Rupee)"

// In different locales
$formatter->format($price, [
    'template' => '%price% %name%',
], 'en_US'); // "1,000.00 Indian Rupee"
$formatter->format($price, [
    'template' => '%price% %name%',
], 'de_AT'); // "1.000,00 Indische Rupie"
```

### Calculating
For calculating prices the package `litipk/php-bignumbers` is used. See their 
documentation [here](http://moneyphp.org/en/stable/index.html).
```php
use Unit\Money\Model\Price;
use Unit\Money\Calculator\PriceCalculator;

$calculator = new PriceCalculator();

// Basic calculations
$price = $calculator->add(new Price(100, 'EUR'), new Price(100, 'EUR')); // The new price represents 2 EUR
$price = $calculator->subtract(new Price(100, 'EUR'), new Price(100, 'EUR'));
$price = $calculator->multiplyWithInteger(new Price(100, 'EUR'), 2);
$fractionalPrice = $calculator->multiplyWithDecimal(new Price(100, 'EUR'), '2.04');

// Allocation per percentages (not implemented yet)
[$a, $b] = $calculator->allocate(new Price(5, 'EUR'), [70, 30]);
$a->getAmount(); // 4
$b->getAmount(); // 1

[$a, $b] = $calculator->allocate(new Price(5, 'EUR'), [30, 70]); // Order matters
$a->getAmount(); // 2
$b->getAmount(); // 3

// Allocation to a number of targets (not implemented yet)
[$a, $b, $c] = $calculator->allocateTo(new Price(5, 'EUR'), 3);
$a->getAmount(); // 267
$b->getAmount(); // 267
$c->getAmount(); // 266
```

## Development
For some development tools the [Symfony binary](https://symfony.com/download) has to be installed:
```console
user@machine:~$ wget https://get.symfony.com/cli/installer -O - | bash
```

Build repo for development:
```console
user@machine:~$ git clone git@github.com:michaelKaefer/money.git
user@machine:~$ cd money/
user@machine:~$ make build-dev
```

Testing:
```console
user@machine:~$ make tests
// Build PHP code coverage
user@machine:~$ make code-coverage
```

Linting:
```console
user@machine:~$ make composer-validate
user@machine:~$ make security-check
user@machine:~$ make psalm-dry-run
user@machine:~$ make cs-fixer-dry-run
```

## License
The MIT License (MIT). Please see [License File](https://github.com/michaelKaefer/money/blob/main/LICENSE) for more information.
