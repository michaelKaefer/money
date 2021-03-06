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
use PHPUnit\Framework\TestCase;
use Unit\Money\Calculator\PriceCalculator;
use Unit\Money\Exception\DifferentCurrenciesException;
use Unit\Money\Exception\IntegerOverflowException;
use Unit\Money\Model\Price;

class PriceCalculatorAdditionTest extends TestCase
{
    private PriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    /**
     * @dataProvider add
     *
     * @throws IntegerOverflowException|DifferentCurrenciesException
     */
    public function testAdd(Price $a, Price $b, int $expectedAmount, string $expectedCurrency): void
    {
        $price = $this->calculator->add($a, $b);

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame($expectedAmount, $price->getAmount());
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function add(): Generator
    {
        yield [
            new Price(1, 'EUR'),
            new Price(2, 'EUR'),
            3, 'EUR',
        ];
        yield [
            new Price(-1, 'EUR'),
            new Price(2, 'EUR'),
            1, 'EUR',
        ];
        yield [
            new Price(1, 'EUR'),
            new Price(-2, 'EUR'),
            -1, 'EUR',
        ];
        yield [
            new Price(-1, 'EUR'),
            new Price(-2, 'EUR'),
            -3, 'EUR',
        ];
    }

    /**
     * @throws IntegerOverflowException|DifferentCurrenciesException
     */
    public function testAddDoesNotAllowMultipleCurrencies(): void
    {
        $this->expectException(DifferentCurrenciesException::class);
        $this->expectExceptionMessage('For calculations all amounts must be of the same currency but different currencies where found: EUR, USD.');

        $this->calculator->add(new Price(1, 'EUR'), new Price(2, 'USD'));
    }

    /**
     * @throws IntegerOverflowException|DifferentCurrenciesException
     */
    public function testAddDoesNotAllowTooHighResults(): void
    {
        $this->expectException(IntegerOverflowException::class);
        $this->expectExceptionMessage('Price calculation resulted in an integer overflow.');

        // On 64-bit platforms PHP_INT_MAX usually is: 9223372036854775807
        $this->calculator->add(new Price(PHP_INT_MAX, 'EUR'), new Price(1, 'EUR'));
    }

    /**
     * @throws IntegerOverflowException|DifferentCurrenciesException
     */
    public function testAddDoesNotAllowTooLowResults(): void
    {
        $this->expectException(IntegerOverflowException::class);
        $this->expectExceptionMessage('Price calculation resulted in an integer overflow.');

        // On 64-bit platforms PHP_INT_MAX usually is: -9223372036854775808
        $this->calculator->add(new Price(PHP_INT_MIN, 'EUR'), new Price(-1, 'EUR'));
    }
}
