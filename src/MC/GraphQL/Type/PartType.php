<?php

declare(strict_types=1);

namespace App\MC\GraphQL\Type;

use App\GraphQL\Type\Types;
use App\Manufacturer\Entity\ManufacturerView;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use App\Part\Entity\PartView;
use GraphQL\Type\Definition\ObjectType;
use Money\Money;

final class PartType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => [
                    'type' => Types::nonNull(Types::id()),
                    'resolve' => fn (PartView $partView): PartId => $partView->id,
                ],
                'name' => [
                    'type' => Types::nonNull(Types::string()),
                    'resolve' => fn (PartView $partView): string => $partView->name,
                ],
                'number' => [
                    'type' => Types::nonNull(Types::string()),
                    'resolve' => fn (PartView $partView): PartNumber => $partView->number,
                ],
                'universal' => [
                    'type' => Types::nonNull(Types::boolean()),
                    'resolve' => fn (PartView $partView): bool => $partView->isUniversal,
                ],
                'price' => [
                    'type' => Types::nonNull(Types::money()),
                    'resolve' => fn (PartView $partView): Money => $partView->price,
                ],
                'discount' => [
                    'type' => Types::nonNull(Types::money()),
                    'resolve' => fn (PartView $partView): Money => $partView->discount,
                ],
                'manufacturer' => [
                    'type' => Types::nonNull(Types::manufacturer()),
                    'resolve' => fn (PartView $partView): ManufacturerView => $partView->manufacturer,
                ],
            ],
            'interfaces' => [
                Types::node(),
            ],
        ];

        parent::__construct($config);
    }
}
