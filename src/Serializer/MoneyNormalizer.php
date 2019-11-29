<?php

declare(strict_types=1);

namespace App\Serializer;

use function assert;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyNormalizer implements NormalizerInterface
{
    private MoneyFormatter $formatter;

    private DecimalMoneyFormatter $decimalMoneyFormatter;

    public function __construct(MoneyFormatter $formatter, DecimalMoneyFormatter $decimalMoneyFormatter)
    {
        $this->formatter = $formatter;
        $this->decimalMoneyFormatter = $decimalMoneyFormatter;
    }

    /**
     * @param mixed $money
     *
     * @return array<string>
     */
    public function normalize($money, string $format = null, array $context = []): array
    {
        assert($money instanceof Money);

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
