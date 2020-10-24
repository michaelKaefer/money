<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Test\Formatter;

use Generator;
use Litipk\BigNumbers\Decimal;
use PHPUnit\Framework\TestCase;
use Twig\Error\RuntimeError;
use Twig\Extra\Intl\IntlExtension;
use Unit\Money\Formatter\PriceFormatter;
use Unit\Money\Model\AbstractPrice;
use Unit\Money\Model\FractionalPrice;
use Unit\Money\Model\Price;

class PriceFormatterTest extends TestCase
{
    private PriceFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new PriceFormatter(new IntlExtension());
    }

    /**
     * @dataProvider format
     *
     * @throws RuntimeError
     */
    public function testFormat(AbstractPrice $price, string $expected)
    {
        $this->assertSame($expected, $this->formatter->format($price));
    }

    public function format(): Generator
    {
        yield [
            new Price(199999, 'EUR'),
            '€1,999.99',
        ];
        yield [
            new Price(1999, 'EUR'),
            '€19.99',
        ];
        yield [
            new Price(199, 'EUR'),
            '€1.99',
        ];
        yield [
            new Price(190, 'EUR'),
            '€1.90',
        ];
        yield [
            new Price(2, 'EUR'),
            '€0.02',
        ];
        yield [
            // Currency without fraction digits
            new Price(199, 'JPY'),
            '¥199',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('34524.3599999'), 'EUR'),
            '€34,524.36',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('34524.351111'), 'EUR'),
            '€34,524.35',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('34524.35'), 'EUR'),
            '€34,524.35',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('2'), 'EUR'),
            '€2.00',
        ];
        yield [
            new FractionalPrice(Decimal::fromString('0.02'), 'EUR'),
            '€0.02',
        ];
    }

    /**
     * Integration test to see if the attributes for IntlExtension::formatCurrency() are working as expected.
     *
     * @throws RuntimeError
     */
    public function testFormatAttributes()
    {
        $this->assertSame('€1,999.9900', $this->formatter->format(new Price(199999, 'EUR'), [
            'fraction_digit' => 4,
        ]));
    }

    /**
     * Integration test to see if the locale for IntlExtension::formatCurrency() is working as expected.
     *
     * @throws RuntimeError
     */
    public function testFormatLocale()
    {
        $this->assertSame("1.999,99\u{a0}€", $this->formatter->format(new Price(199999, 'EUR'), [], 'de'));
    }

    /**
     * @throws RuntimeError
     */
    public function testThrowsAnExceptionIfUnknownClassIsUsed()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Supported classes are: Unit\Money\Model\Price, Unit\Money\Model\FractionalPrice.');

        $this->formatter->format(new class() extends AbstractPrice {
        });
    }

    /**
     * @throws RuntimeError
     */
    public function testThrowsOnInvalidTemplate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "template" option has to be a string, e.g.: "%price% %code%".');

        $this->formatter->format(new Price(10, 'EUR'), ['template' => 123]);
    }

    /**
     * @dataProvider template
     *
     * @throws RuntimeError
     */
    public function testTemplate(AbstractPrice $price, string $template, string $expected)
    {
        $this->assertSame($expected, $this->formatter->format($price, ['template' => $template]));

        $formatter = new PriceFormatter(new IntlExtension(), $template);
        $this->assertSame($expected, $formatter->format($price));
    }

    public function template(): Generator
    {
        yield [
            new Price(199999, 'EUR'),
            '%price% %code%',
            "1,999.99\u{a0}EUR",
        ];
        yield [
            new Price(199999, 'EUR'),
            '%price% foo %code% %price% %symbol% %name%',
            "1,999.99\u{a0}foo\u{a0}EUR\u{a0}1,999.99\u{a0}€\u{a0}Euro",
        ];
    }
}
