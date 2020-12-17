<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

final class DateType extends ScalarType
{
    private const FORMAT = 'Y-m-d';

    /**
     * {@inheritDoc}
     */
    public function serialize($value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(self::FORMAT);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function parseValue($value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat(self::FORMAT, $value);

        if (false === $date) {
            throw new Error('Cannot represent following value as Date: '.Utils::printSafeJson($value));
        }

        return $date;
    }

    /**
     * {@inheritDoc}
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): DateTimeImmutable
    {
        if (!$valueNode instanceof StringValueNode) {
            /** @phpstan-ignore-next-line */
            throw new Error('Query error: Can only parse strings got: '.$valueNode->kind, [$valueNode]);
        }

        return $this->parseValue($valueNode->value);
    }
}
