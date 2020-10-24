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
use Unit\Money\Exception\IntegerOverflowException;
use Unit\Money\Model\Price;

class PriceCalculatorMultiplicationWithIntegerTest extends TestCase
{
    private PriceCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    /**
     * @dataProvider multiplyWithInteger
     *
     * @throws IntegerOverflowException
     */
    public function testMultiplyWithInteger(Price $p, int $number, int $expectedAmount, string $expectedCurrency): void
    {
        $price = $this->calculator->multiplyWithInteger($p, $number);

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame($expectedAmount, $price->getAmount());
        $this->assertSame($expectedCurrency, $price->getCurrency());
    }

    public function multiplyWithInteger(): Generator
    {
        yield [
            new Price(2, 'EUR'),
            1,
            2, 'EUR',
        ];
        yield [
            new Price(2, 'EUR'),
            4,
            8, 'EUR',
        ];
        yield [
            new Price(-1, 'EUR'),
            5,
            -5, 'EUR',
        ];
        yield [
            new Price(-2, 'EUR'),
            -4,
            8, 'EUR',
        ];
    }

    /**
     * @throws IntegerOverflowException
     */
    public function testMultiplyWithIntegerDoesNotAllowTooHighResults(): void
    {
        $this->expectException(IntegerOverflowException::class);
        $this->expectExceptionMessage('Price calculation resulted in an integer overflow.');

        // On 64-bit platforms PHP_INT_MAX usually is: 9223372036854775807
        $this->calculator->multiplyWithInteger(new Price(PHP_INT_MAX, 'EUR'), 2);
    }

    /**
     * @throws IntegerOverflowException
     */
    public function testMultiplyWithIntegerDoesNotAllowTooLowResults(): void
    {
        $this->expectException(IntegerOverflowException::class);
        $this->expectExceptionMessage('Price calculation resulted in an integer overflow.');

        // On 64-bit platforms PHP_INT_MAX usually is: -9223372036854775808
        $this->calculator->multiplyWithInteger(new Price(PHP_INT_MIN, 'EUR'), 2);
    }
}
