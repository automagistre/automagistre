<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use function is_string;

final class EmailType extends ScalarType
{
    /**
     * {@inheritDoc}
     */
    public function serialize($value): string
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function parseValue($value): string
    {
        $strictValidator = new \Egulias\EmailValidator\EmailValidator();

        if (!is_string($value) || !$strictValidator->isValid($value, new NoRFCWarningsValidation())) {
            throw new Error('Cannot represent following value as Email: '.Utils::printSafeJson($value));
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): string
    {
        if (!$valueNode instanceof StringValueNode) {
            /** @phpstan-ignore-next-line */
            throw new Error('Query error: Can only parse strings got: '.$valueNode->kind, [$valueNode]);
        }

        return $this->parseValue($valueNode->value);
    }
}
