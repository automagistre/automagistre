<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use InvalidArgumentException;
use Money\Currency;
use Money\Money;
use function count;
use function explode;
use function sprintf;

final class MoneyInputType extends ScalarType
{
    public $name = 'MoneyInput';

    public $description = 'The `String` scalar type represents as Currency and Amount divided by space. Example: "USD 100500" represent as $1005.00';

    /**
     * {@inheritDoc}
     */
    public function serialize($value): string
    {
        if ($value instanceof Money) {
            return sprintf('%s %s', $value->getCurrency()->getCode(), $value->getAmount());
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function parseValue($value): Money
    {
        $explode = explode(' ', $value);

        if (2 !== count($explode)) {
            throw new Error('Cannot represent following value as Money: '.Utils::printSafeJson($value));
        }

        [$currencyCode, $amount] = $explode;

        try {
            $currency = new Currency($currencyCode);
        } catch (InvalidArgumentException $e) {
            throw new Error('Cannot represent following value as Currency: '.Utils::printSafeJson($currencyCode));
        }

        try {
            return new Money($amount, $currency);
        } catch (InvalidArgumentException $e) {
            throw new Error('Cannot represent following value as Money: '.Utils::printSafeJson($amount));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): Money
    {
        if (!$valueNode instanceof StringValueNode) {
            /** @phpstan-ignore-next-line */
            throw new Error('Query error: Can only parse strings got: '.$valueNode->kind, [$valueNode]);
        }

        return $this->parseValue($valueNode->value);
    }
}
