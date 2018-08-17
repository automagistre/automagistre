<?php

declare(strict_types=1);

namespace App\Serializer;

use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyNormalizer implements NormalizerInterface
{
    /**
     * @var MoneyFormatter
     */
    private $formatter;

    /**
     * @var DecimalMoneyFormatter
     */
    private $decimalMoneyFormatter;

    public function __construct(MoneyFormatter $formatter, DecimalMoneyFormatter $decimalMoneyFormatter)
    {
        $this->formatter = $formatter;
        $this->decimalMoneyFormatter = $decimalMoneyFormatter;
    }

    /**
     * @param Money       $money
     * @param string|null $format
     */
    public function normalize($money, $format = null, array $context = []): array
    {
        return [
            'amount' => $this->decimalMoneyFormatter->format($money),
            'currency' => $money->getCurrency()->getCode(),
            'formatted' => $this->formatter->format($money),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Money;
    }
}
