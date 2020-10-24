<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Model;

final class Price extends AbstractPrice
{
    /**
     * An integer which represents the price amount in the smallest complete unit of the currency.
     *
     * For example:
     *  - an amount of 1999 together with the currency "EUR" represents 19.99 EUR (the Euro has 2 fraction digits)
     *  - an amount of 71 together with the currency "JPY" represents 71 JPY (the Japanese yen has 0 fraction digits)
     *
     * Use cases are:
     *  - The price of a product or service
     *  - The price a customer has to pay
     *  - The price of a cart
     *  - etc.
     * Cases for which this class should not be used:
     *  - Use cases where more precise prices are needed, for example it is not possible to represent prices like
     *    5.001 EUR with this class.
     *
     * For more information see:
     *  - To get an impression of fractional digits see: https://en.wikipedia.org/wiki/ISO_4217
     *
     * The downsides to use an integer are:
     *  - The highest number which can be represented is platform-dependent.
     *  - The highest number which can be represented is limited. (Check your PHP_INT_MAX and also see
     *    https://www.php.net/manual/en/language.types.integer.php.) Most times it is: 9,223,372,036,854,775,807
     *    which seems enough for most use cases. But on some platforms it is only about 2 billion.
     *  - The lowest number is limited too, most times it is: -9,223,372,036,854,775,807
     *  - The precision is limited to the smallest complete unit of the currency. This is not a downside at all
     *    because for this use case there is another class Unit\Money\FractionalPrice which is able to handle more
     *    precise prices. Martin Fowler proposes to have 2 different classes for this 2 use cases.
     */
    private int $amount;

    public function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
