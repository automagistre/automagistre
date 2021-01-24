<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use App\Shared\Identifier\Identifier;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function is_string;

final class UuidType extends ScalarType
{
    /**
     * {@inheritDoc}
     */
    public function serialize($value): string
    {
        if ($value instanceof Identifier || $value instanceof UuidInterface) {
            return $value->toString();
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function parseValue($value): UuidInterface
    {
        if (!is_string($value) || !Uuid::isValid($value)) {
            throw new Error('Cannot represent following value as UUID: '.Utils::printSafeJson($value));
        }

        return Uuid::fromString($value);
    }

    /**
     * {@inheritDoc}
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): UuidInterface
    {
        // Note: throwing GraphQL\Error\Error vs \UnexpectedValueException to benefit from GraphQL
        // error location in query:
        if (!$valueNode instanceof StringValueNode) {
            /** @phpstan-ignore-next-line */
            throw new Error('Query error: Can only parse strings got: '.$valueNode->kind, [$valueNode]);
        }

        return $this->parseValue($valueNode->value);
    }
}
