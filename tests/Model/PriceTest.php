<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Test\Model;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Validation;
use Unit\Money\Model\Price;

class PriceTest extends TestCase
{
    public function testCurrencyValidation(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $violations = $validator->validate(new Price(10, 'EUR'));

        $this->assertCount(0, $violations->findByCodes(Currency::NO_SUCH_CURRENCY_ERROR));

        $violations = $validator->validate(new Price(10, 'foo'));

        $this->assertCount(1, $violations->findByCodes(Currency::NO_SUCH_CURRENCY_ERROR));
    }
}
