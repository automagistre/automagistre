<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DivisoredNumberToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    /**
     * @var int
     */
    private $divisor;

    public function __construct(
        ?int $scale = 2,
        ?bool $grouping = true,
        ?int $roundingMode = self::ROUND_HALF_UP,
        ?int $divisor = 1
    ) {
        if (null === $grouping) {
            $grouping = true;
        }

        if (null === $scale) {
            $scale = 2;
        }

        parent::__construct($scale, $grouping, $roundingMode);

        $this->divisor = $divisor ?? 1;
    }

    /**
     * @param mixed $value
     *
     * @throws TransformationFailedException
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        if (!\is_int($value) && !\is_float($value)) {
            throw new TransformationFailedException('Expected a numeric.');
        }

        $value /= $this->divisor;

        /* @psalm-var int|float $value */
        return parent::transform($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): int
    {
        /** @var int|float|null $value */
        $value = parent::reverseTransform($value);

        if (\is_numeric($value)) {
            $value *= $this->divisor;
        }

        if (false !== \strpos((string) $value, '.')) {
            throw new TransformationFailedException('Value must be integer after reverseTransformation');
        }

        return (int) $value;
    }
}
