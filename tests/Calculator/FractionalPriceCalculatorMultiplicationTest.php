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
use Unit\Money\Model\FractionalPrice;

class FractionalPriceCalculatorMultiplicationTest extends TestCase
{
    private FractionalPriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new FractionalPriceCalculator();
    }

    /**
     * @dataProvider multiply
     */
    public function testMultiply(FractionalPrice $a, Decimal $multiplier, Decimal $expectedDecimal, string $expectedCurrency): void
    {
        $price = $this->calculator->multiply($a, $multiplier);

        $this->assertInstanceOf(FractionalPrice::class, $price);
        $this->assertTrue($price->getAmount()->equals($expectedDecimal), sprintf('In Unit\Money\Calculator\FractionalPriceCalculator on addition the amount %s is equal to the expected amount %s', (string) $price->getAmount(), (string) $expectedDecimal));
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function multiply(): Generator
    {
        yield [
            new FractionalPrice(Decimal::fromString('1'), 'EUR'),
            Decimal::fromString('3'),
            Decimal::fromString('3'), 'EUR',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('3'), 'EUR'),
            Decimal::fromString('-4'),
            Decimal::fromString('-12'), 'EUR',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('335.3454'), 'EUR'),
            Decimal::fromString('123.65'),
            Decimal::fromString('41465.458710'), 'EUR',
        ];
    }
}
