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

class PriceCalculatorCalculateNetTest extends TestCase
{
    private PriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    /**
     * @dataProvider calculateGross
     */
    public function testCalculateGross(Price $a, string $taxRate, Decimal $expectedAmount, string $expectedCurrency): void
    {
        $price = $this->calculator->calculateGross($a, $taxRate);

        $this->assertInstanceOf(FractionalPrice::class, $price);
        $this->assertTrue($price->getAmount()->equals($expectedAmount));
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function calculateGross(): Generator
    {
        yield [
            new Price(10, 'EUR'),
            '20',
            Decimal::fromString('12'), 'EUR',
        ];
    }
}
