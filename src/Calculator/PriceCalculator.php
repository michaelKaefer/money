<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Calculator;

use Litipk\BigNumbers\Decimal;
use Unit\Money\Exception\DifferentCurrenciesException;
use Unit\Money\Exception\IntegerOverflowException;
use Unit\Money\Model\FractionalPrice;
use Unit\Money\Model\Price;

final class PriceCalculator extends AbstractCalculator
{
    public function toFractionalPrice(Price $price): FractionalPrice
    {
        return new FractionalPrice(Decimal::fromInteger($price->getAmount()), $price->getCurrency());
    }

    /**
     * Adds two prices. Since Unit\Money\Price is immutable a new instance is returned.
     *
     * @throws DifferentCurrenciesException
     * @throws IntegerOverflowException
     */
    public function add(Price $a, Price $b): Price
    {
        $this->throwExceptionIfDifferentCurrencies([$a, $b]);

        $amount = $a->getAmount() + $b->getAmount();

        $this->throwExceptionIfIntegerOverflow($amount);

        return new Price($amount, $a->getCurrency());
    }

    /**
     * Subtracts two prices. Since Unit\Money\Price is immutable a new instance is returned.
     *
     * @throws DifferentCurrenciesException
     * @throws IntegerOverflowException
     */
    public function subtract(Price $a, Price $b): Price
    {
        $this->throwExceptionIfDifferentCurrencies([$a, $b]);

        $amount = $a->getAmount() - $b->getAmount();

        $this->throwExceptionIfIntegerOverflow($amount);

        return new Price($amount, $a->getCurrency());
    }

    /**
     * Multiplies a price with an integer. Since Unit\Money\Price is immutable a new instance is returned.
     *
     * @throws IntegerOverflowException
     */
    public function multiplyWithInteger(Price $price, int $number): Price
    {
        $amount = $price->getAmount() * $number;

        $this->throwExceptionIfIntegerOverflow($amount);

        return new Price($amount, $price->getCurrency());
    }

    /**
     * Multiplies a price with a decimal.
     */
    public function multiplyWithDecimal(Price $price, string $number): FractionalPrice
    {
        $amount = Decimal::fromString($number)
            ->mul(Decimal::fromInteger($price->getAmount()));

        return new FractionalPrice($amount, $price->getCurrency());
    }

    public function calculateGross(Price $price, string $rate): FractionalPrice
    {
        $ratePlus100 = Decimal::fromString($rate)->add(Decimal::fromString('100'));
        $priceDividedBy100 = Decimal::fromInteger($price->getAmount())->div(Decimal::fromString('100'));

        return new FractionalPrice($priceDividedBy100->mul($ratePlus100), $price->getCurrency());
    }

    public function calculateNet(Price $price, string $rate): FractionalPrice
    {
        $ratePlus100 = Decimal::fromString($rate)->add(Decimal::fromString('100'));
        $priceDividedByRatePlus100 = Decimal::fromInteger($price->getAmount())->div($ratePlus100);

        return new FractionalPrice($priceDividedByRatePlus100->mul(Decimal::fromString('100')), $price->getCurrency());
    }

    /**
     * @param int|float $amount
     *
     * @throws IntegerOverflowException
     */
    private function throwExceptionIfIntegerOverflow($amount): void
    {
        if (\is_float($amount)) {
            // See: https://www.php.net/manual/en/language.types.integer.php
            throw new IntegerOverflowException('Price calculation resulted in an integer overflow.');
        }
    }
}
