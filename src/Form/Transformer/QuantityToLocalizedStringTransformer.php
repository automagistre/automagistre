<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class QuantityToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    private $divisor;

    public function __construct($scale = 2, $grouping = true, $roundingMode = self::ROUND_HALF_UP, $divisor = 1)
    {
        if (null === $grouping) {
            $grouping = true;
        }

        if (null === $scale) {
            $scale = 2;
        }

        parent::__construct($scale, $grouping, $roundingMode);

        if (null === $divisor) {
            $divisor = 1;
        }

        $this->divisor = $divisor;
    }

    /**
     * @param float|int $value
     *
     * @return string
     *
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        if (null !== $value) {
            if (!is_numeric($value)) {
                throw new TransformationFailedException('Expected a numeric.');
            }

            $value /= $this->divisor;
        }

        return parent::transform($value);
    }

    /**
     * @param string $value
     *
     * @return int|string
     */
    public function reverseTransform($value)
    {
        $value = parent::reverseTransform($value);

        if (null !== $value) {
            $value *= $this->divisor;
        }

        return $value;
    }
}
