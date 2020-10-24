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
use Unit\Money\Calculator\PriceCalculator;
use Unit\Money\Model\FractionalPrice;
use Unit\Money\Model\Price;

class PriceCalculatorMultiplicationWithFloatTest extends TestCase
{
    private PriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    /**
     * @dataProvider multiplyWithFloat
     */
    public function testMultiplyWithFloat(Price $p, string $number, Decimal $expectedDecimal, string $expectedCurrency): void
    {
        $price = $this->calculator->multiplyWithDecimal($p, $number);

        $this->assertInstanceOf(FractionalPrice::class, $price);
        $this->assertTrue($price->getAmount()->equals($expectedDecimal), sprintf('In Unit\Money\Calculator\PriceCalculator on multiplication with a float the amount %s is equal to the expected amount %s', (string) $p->getAmount(), (string) $expectedDecimal));
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function multiplyWithFloat(): Generator
    {
        yield [
            new Price(2, 'EUR'),
            '1.0',
            Decimal::fromString('2'), 'EUR',
        ];
        yield [
            new Price(-2, 'EUR'),
            '4.55',
            Decimal::fromString('-9.1'), 'EUR',
        ];
        yield [
            new Price(54653623562, 'EUR'),
            '4.55865',
            Decimal::fromString('249146741050.9113'), 'EUR',
        ];
    }
}
