<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

final class PhoneNumberType extends ScalarType
{
    /**
     * {@inheritDoc}
     */
    public function serialize($value): string
    {
        if ($value instanceof PhoneNumber) {
            return PhoneNumberUtil::getInstance()->format($value, PhoneNumberFormat::E164);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function parseValue($value): PhoneNumber
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $phoneNumberUtil->parse($value);
        } catch (NumberParseException $e) {
            throw new Error('Cannot represent following value as PhoneNumber: '.Utils::printSafeJson($value));
        }

        if (false === $phoneNumberUtil->isValidNumber($phoneNumber)) {
            throw new Error('The following PhoneNumber is not valid: '.Utils::printSafeJson($value));
        }

        return $phoneNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): PhoneNumber
    {
        if (!$valueNode instanceof StringValueNode) {
            /** @phpstan-ignore-next-line */
            throw new Error('Query error: Can only parse strings got: '.$valueNode->kind, [$valueNode]);
        }

        return $this->parseValue($valueNode->value);
    }
}
