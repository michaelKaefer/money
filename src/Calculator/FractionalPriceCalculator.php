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
use Unit\Money\Model\FractionalPrice;

final class FractionalPriceCalculator extends AbstractCalculator
{
    /**
     * Adds two prices. Since Unit\Money\FractionalPrice is immutable a new instance is returned.
     *
     * @throws DifferentCurrenciesException
     */
    public function add(FractionalPrice $a, FractionalPrice $b): FractionalPrice
    {
        $this->throwExceptionIfDifferentCurrencies([$a, $b]);

        $amount = $a->getAmount()->add($b->getAmount());

        return new FractionalPrice($amount, $a->getCurrency());
    }

    /**
     * Subtracts two prices. Since Unit\Money\FractionalPrice is immutable a new instance is returned.
     *
     * @throws DifferentCurrenciesException
     */
    public function subtract(FractionalPrice $a, FractionalPrice $b): FractionalPrice
    {
        $this->throwExceptionIfDifferentCurrencies([$a, $b]);

        $amount = $a->getAmount()->sub($b->getAmount());

        return new FractionalPrice($amount, $a->getCurrency());
    }

    /**
     * Multiplies a price with a number. Since Unit\Money\FractionalPrice is immutable a new instance is returned.
     */
    public function multiply(FractionalPrice $price, Decimal $number): FractionalPrice
    {
        $amount = $price->getAmount()->mul($number);

        return new FractionalPrice($amount, $price->getCurrency());
    }

    public function calculateGross(FractionalPrice $price, string $rate): FractionalPrice
    {
        $ratePlus100 = Decimal::fromString($rate)->add(Decimal::fromString('100'));
        $priceDividedBy100 = $price->getAmount()->div(Decimal::fromString('100'));

        return new FractionalPrice($priceDividedBy100->mul($ratePlus100), $price->getCurrency());
    }

    public function calculateNet(FractionalPrice $price, string $rate): FractionalPrice
    {
        $ratePlus100 = Decimal::fromString($rate)->add(Decimal::fromString('100'));
        $priceDividedByRatePlus100 = $price->getAmount()->div($ratePlus100);

        return new FractionalPrice($priceDividedByRatePlus100->mul(Decimal::fromString('100')), $price->getCurrency());
    }

    /**
     * @throws DifferentCurrenciesException
     */
    public function calculateTaxRate(FractionalPrice $net, FractionalPrice $gross): Decimal
    {
        $this->throwExceptionIfDifferentCurrencies([$net, $gross]);

        $netDividedBy100 = $net->getAmount()->div(Decimal::fromString('100'));
        $grossDividedByNetDividedBy100 = $gross->getAmount()->div($netDividedBy100);

        return $grossDividedByNetDividedBy100->sub(Decimal::fromString('100'));
    }

    /**
     * @throws DifferentCurrenciesException
     */
    public function calculateTax(FractionalPrice $net, FractionalPrice $gross): Decimal
    {
        $this->throwExceptionIfDifferentCurrencies([$net, $gross]);

        return $gross->getAmount()->sub($net->getAmount());
    }
}
