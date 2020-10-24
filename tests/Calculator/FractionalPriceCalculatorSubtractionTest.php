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

class FractionalPriceCalculatorSubtractionTest extends TestCase
{
    private FractionalPriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new FractionalPriceCalculator();
    }

    /**
     * @dataProvider subtract
     *
     * @throws DifferentCurrenciesException
     */
    public function testSubtract(FractionalPrice $a, FractionalPrice $b, Decimal $expectedDecimal, string $expectedCurrency): void
    {
        $price = $this->calculator->subtract($a, $b);

        $this->assertInstanceOf(FractionalPrice::class, $price);
        $this->assertTrue($price->getAmount()->equals($expectedDecimal), sprintf('In Unit\Money\Calculator\FractionalPriceCalculator on subtraction the amount %s is equal to the expected amount %s', (string) $price->getAmount(), (string) $expectedDecimal));
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function subtract(): Generator
    {
        yield [
            new FractionalPrice(Decimal::fromString('1'), 'EUR'),
            new FractionalPrice(Decimal::fromString('2'), 'EUR'),
            Decimal::fromString('-1'), 'EUR',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('-1'), 'EUR'),
            new FractionalPrice(Decimal::fromString('2'), 'EUR'),
            Decimal::fromString('-3'), 'EUR',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('1'), 'EUR'),
            new FractionalPrice(Decimal::fromString('-2'), 'EUR'),
            Decimal::fromString('3'), 'EUR',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('-1'), 'EUR'),
            new FractionalPrice(Decimal::fromString('-2'), 'EUR'),
            Decimal::fromString('1'), 'EUR',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('234234.23234'), 'EUR'),
            new FractionalPrice(Decimal::fromString('2342.214'), 'EUR'),
            Decimal::fromString('231892.01834'), 'EUR',
        ];
    }

    /**
     * @throws DifferentCurrenciesException
     */
    public function testSubtractDoesNotAllowMultipleCurrencies(): void
    {
        $this->expectException(DifferentCurrenciesException::class);
        $this->expectExceptionMessage('For calculations all amounts must be of the same currency but different currencies where found: EUR, USD.');

        $this->calculator->subtract(new FractionalPrice(Decimal::fromString('1'), 'EUR'), new FractionalPrice(Decimal::fromString('2'), 'USD'));
    }
}
