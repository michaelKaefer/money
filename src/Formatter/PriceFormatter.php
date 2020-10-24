<?php

declare(strict_types=1);

/*
 * This file is part of money.
 * (c) Michael Käfer <michael.kaefer1@gmx.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit\Money\Formatter;

use InvalidArgumentException;
use Symfony\Component\Intl\Currencies;
use Twig\Error\RuntimeError;
use Twig\Extra\Intl\IntlExtension;
use Unit\Money\Model\AbstractPrice;
use Unit\Money\Model\FractionalPrice;
use Unit\Money\Model\Price;

class PriceFormatter
{
    private IntlExtension $intlExtension;
    private ?string $template;

    public function __construct(IntlExtension $intlExtension, string $template = null)
    {
        $this->intlExtension = $intlExtension;
        $this->template = $template;
    }

    /**
     * @throws RuntimeError
     */
    public function format(AbstractPrice $price, array $attributes = [], string $locale = null): string
    {
        // Get custom template
        $template = $this->template;
        if (\array_key_exists('template', $attributes)) {
            if (!\is_string($attributes['template'])) {
                throw new InvalidArgumentException('The "template" option has to be a string, e.g.: "%price% %code%".');
            }
            $template = $attributes['template'];
            unset($attributes['template']);
        }

        // Get the amount as a float value
        if ($price instanceof Price) {
            if (0 !== $fractionDigits = Currencies::getFractionDigits($price->getCurrency())) {
                $amount = $price->getAmount() / pow(10, $fractionDigits); // E.g.: 19.93
            } else {
                $amount = (float) ($price->getAmount()); // E.g.: 12.0
            }
        } elseif ($price instanceof FractionalPrice) {
            $amount = $price->getAmount()->asFloat();
        } else {
            throw new InvalidArgumentException(sprintf('Supported classes are: %s.', implode(', ', [Price::class, FractionalPrice::class])));
        }

        // Use Twig to format the price
        $formatted = $this->intlExtension->formatCurrency(
            $amount,
            $price->getCurrency(),
            $attributes,
            $locale
        );

        // Apply custom template
        if (null === $template) {
            return $formatted;
        }

        return $this->render($formatted, $template, $price->getCurrency(), $locale);
    }

    private function render(string $formattedPrice, string $template, string $currency, ?string $locale): string
    {
        $price = str_replace(Currencies::getSymbol($currency), '', $formattedPrice);
        $price = str_replace("\xc2\xa0", '', $price); // Remove non-breaking spaces

        $formatted = preg_replace('/%name%/', Currencies::getName($currency, $locale), $template);
        $formatted = preg_replace('/%symbol%/', Currencies::getSymbol($currency, $locale), $formatted);
        $formatted = preg_replace('/%code%/', $currency, $formatted);
        $formatted = preg_replace('/%price%/', $price, $formatted);

        $formatted = str_replace(' ', "\xc2\xa0", $formatted); // Add non-breaking spaces

        return $formatted;
    }
}
