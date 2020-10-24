<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Test\Calculator;

use Litipk\BigNumbers\Decimal;
use PHPUnit\Framework\TestCase;
use Unit\Money\Calculator\PriceCalculator;
use Unit\Money\Model\FractionalPrice;
use Unit\Money\Model\Price;

class PriceCalculatorToFractionalPriceTest extends TestCase
{
    private PriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    public function testToFractionalPrice(): void
    {
        $price = $this->calculator->toFractionalPrice(new Price(45, 'EUR'));

        $this->assertInstanceOf(FractionalPrice::class, $price);
        $this->assertTrue($price->getAmount()->equals(Decimal::fromString('45')));
        $this->assertSame('EUR', $price->getCurrency());
    }
}
