<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Test\Calculator;

use Generator;
use Litipk\BigNumbers\Decimal;
use PHPUnit\Framework\TestCase;
use Unit\Money\Calculator\FractionalPriceCalculator;
use Unit\Money\Exception\DifferentCurrenciesException;
use Unit\Money\Model\FractionalPrice;

class FractionalPriceCalculatorCalculateTaxRateTest extends TestCase
{
    private FractionalPriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new FractionalPriceCalculator();
    }

    /**
     * @dataProvider calculateTaxRate
     *
     * @throws DifferentCurrenciesException
     */
    public function testCalculateTaxRate(FractionalPrice $a, FractionalPrice $b, Decimal $expected): void
    {
        $taxRate = $this->calculator->calculateTaxRate($a, $b);

        $this->assertInstanceOf(Decimal::class, $taxRate);
        $this->assertTrue($taxRate->equals($expected), sprintf('In Unit\Money\Calculator\FractionalPriceCalculator on tax rate calculation the amount %s is equal to the expected amount %s', (string) $taxRate, (string) $expected));
    }

    public function calculateTaxRate(): Generator
    {
        yield [
            new FractionalPrice(Decimal::fromString('10'), 'EUR'),
            new FractionalPrice(Decimal::fromString('12'), 'EUR'),
            Decimal::fromString('20'),
        ];
    }
}
