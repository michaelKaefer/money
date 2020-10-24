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

class FractionalPriceCalculatorCalculateNetTest extends TestCase
{
    private FractionalPriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new FractionalPriceCalculator();
    }

    /**
     * @dataProvider calculateNet
     */
    public function testCalculateNet(FractionalPrice $a, string $taxRate, Decimal $expectedAmount, string $expectedCurrency): void
    {
        $price = $this->calculator->calculateNet($a, $taxRate);

        $this->assertInstanceOf(FractionalPrice::class, $price);
        $this->assertTrue($price->getAmount()->equals($expectedAmount));
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function calculateNet(): Generator
    {
        yield [
            new FractionalPrice(Decimal::fromString('12'), 'EUR'),
            '20',
            Decimal::fromString('10'), 'EUR',
        ];
    }
}
