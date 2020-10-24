<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Model;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractPrice
{
    /**
     * An ISO 4217 currency code like "EUR", "USD", etc.
     *
     * For more information see:
     *  - https://symfony.com/doc/current/reference/constraints/Currency.html
     *  - https://en.wikipedia.org/wiki/ISO_4217
     *
     * @Assert\Currency
     */
    protected string $currency;

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
