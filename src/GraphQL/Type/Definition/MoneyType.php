<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\ObjectType;
use Money\Money;

final class MoneyType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'amount' => [
                    'type' => Types::nonNull(Types::string()),
                    'resolve' => fn (Money $money): string => $money->getAmount(),
                ],
                'currency' => [
                    'type' => Types::nonNull(Types::string()),
                    'resolve' => fn (Money $money): string => $money->getCurrency()->getCode(),
                ],
            ],
        ];

        parent::__construct($config);
    }
}
