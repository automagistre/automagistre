<?php

declare(strict_types=1);

namespace App\Tests\Site;

use Generator;

final class MaintenanceTest extends GraphQlWwwTestCase
{
    public function data(): Generator
    {
        yield [
            <<<'GQL'
            {
                maintenances(vehicleId: "1ea88042-e4ff-6faa-80f4-ba1ca6d07248") {
                    id
                    transmission
                    vehicle {
                        id
                        name
                        caseName
                        localizedName
                        manufacturer {
                            id
                            name
                            localizedName
                        }
                        production {
                            from
                            till
                        }
                    }
                    engine {
                        name
                        type
                        airIntake
                        injection
                        capacity
                    }
                    wheelDrive
                    works {
                        id
                        name
                        description
                        parts {
                            part {
                                id
                                name
                                number
                                unit
                                universal
                                price {
                                    amount
                                    currency
                                }
                                discount {
                                    amount
                                    currency
                                }
                                manufacturer {
                                    id
                                    name
                                    localizedName
                                }
                            }
                            quantity
                            recommended
                        }
                        period
                        price {
                            amount
                            currency
                        }
                        recommended
                        position
                    }
                }
            }
            GQL,
            [],
            [
                'data' => [
                    'maintenances' => [
                        0 => [
                            'engine' => [
                                'airIntake' => 'unknown',
                                'capacity' => '0',
                                'injection' => 'unknown',
                                'name' => null,
                                'type' => 'unknown',
                            ],
                            'id' => '1eab7adc-b60e-616e-8fb9-0242c0a81005',
                            'transmission' => 'unknown',
                            'vehicle' => [
                                'caseName' => null,
                                'id' => '1ea88042-e4ff-6faa-80f4-ba1ca6d07248',
                                'localizedName' => null,
                                'manufacturer' => [
                                    'id' => '1ea88058-1c1f-6f20-9482-ba1ca6d07248',
                                    'localizedName' => 'Ниссан',
                                    'name' => 'Nissan',
                                ],
                                'name' => 'GTR',
                                'production' => [
                                    'from' => null,
                                    'till' => null,
                                ],
                            ],
                            'wheelDrive' => 'unknown',
                            'works' => [
                                0 => [
                                    'description' => null,
                                    'id' => '1eab7ada-800b-69d8-a8a9-0242c0a81005',
                                    'name' => 'Work 1',
                                    'parts' => [
                                        0 => [
                                            'part' => [
                                                'discount' => [
                                                    'amount' => '10000',
                                                    'currency' => 'RUB',
                                                ],
                                                'id' => '1ea88126-9b50-62f8-9995-ba1ca6d07248',
                                                'manufacturer' => [
                                                    'id' => '1ea88057-31b5-6c6e-bc87-ba1ca6d07248',
                                                    'localizedName' => 'Тойота',
                                                    'name' => 'Toyota',
                                                ],
                                                'name' => 'Сальник',
                                                'number' => 'PART1NUMBER',
                                                'price' => [
                                                    'amount' => '150000',
                                                    'currency' => 'RUB',
                                                ],
                                                'unit' => 'thing',
                                                'universal' => false,
                                            ],
                                            'quantity' => 1,
                                            'recommended' => false,
                                        ],
                                    ],
                                    'period' => 10,
                                    'position' => 0,
                                    'price' => [
                                        'amount' => '100',
                                        'currency' => 'RUB',
                                    ],
                                    'recommended' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
