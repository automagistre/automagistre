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
     * @throws TransformationFailedException
     *
     * @return string
     */
    public function transform($value): string
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
     * @throws TransformationFailedException
     *
     * @return int
     */
    public function reverseTransform($value): int
    {
        $value = parent::reverseTransform($value);

        if (null !== $value) {
            $value *= $this->divisor;
        }

        if (false !== strpos((string) $value, '.')) {
            throw new TransformationFailedException('Value must be integer after reverseTransformation');
        }

        return (int) $value;
    }
}
