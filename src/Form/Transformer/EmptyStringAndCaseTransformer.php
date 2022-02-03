<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use function is_string;
use function mb_convert_case;
use function trim;
use const MB_CASE_UPPER;

final class EmptyStringAndCaseTransformer implements DataTransformerInterface
{
    private int $case;

    public function __construct(int $case = MB_CASE_UPPER)
    {
        $this->case = $case;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('String was expected.');
        }

        if ('' === trim($value)) {
            return null;
        }

        return mb_convert_case($value, $this->case);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(mixed $value): mixed
    {
        return $this->transform($value);
    }
}
