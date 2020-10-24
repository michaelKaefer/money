<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Exception;

use Exception;
use Throwable;

class DifferentCurrenciesException extends Exception
{
    public function __construct(array $currencies, string $message = 'For calculations all amounts must be of the same currency but different currencies where found: %s.', int $code = 0, Throwable $previous = null)
    {
        $message = sprintf($message, implode(', ', $currencies));
        parent::__construct($message, $code, $previous);
    }
}
