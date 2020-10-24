<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Model;

use Litipk\BigNumbers\Decimal;

final class FractionalPrice extends AbstractPrice
{
    private Decimal $amount;

    public function __construct(Decimal $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): Decimal
    {
        return $this->amount;
    }
}
