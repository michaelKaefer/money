<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Calculator;

use Unit\Money\Exception\DifferentCurrenciesException;
use Unit\Money\Model\AbstractPrice;

abstract class AbstractCalculator
{
    /**
     * @throws DifferentCurrenciesException
     */
    protected function throwExceptionIfDifferentCurrencies(array $prices): void
    {
        $currencies = array_unique(array_map(fn (AbstractPrice $price) => $price->getCurrency(), $prices));

        if (1 < \count($currencies)) {
            throw new DifferentCurrenciesException($currencies);
        }
    }
}
