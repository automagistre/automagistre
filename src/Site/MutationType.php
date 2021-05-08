<?php

declare(strict_types=1);

namespace App\Site;

use App\Appeal\Entity\AppealId;
use App\Appeal\Entity\Calculator;
use App\Appeal\Entity\Call;
use App\Appeal\Entity\Cooperation;
use App\Appeal\Entity\Question;
use App\Appeal\Entity\Schedule;
use App\Appeal\Entity\TireFitting;
use App\GraphQL\Type\Types;
use App\MC\Entity\McEquipmentId;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Enum\TireFittingCategory;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;

final class MutationType extends ObjectType
{
    public function __construct()
    {
        $appealOutputType = new ObjectType([
            'name' => 'createAppealOutput',
            'fields' => fn () => [
                'appealId' => Types::nonNull(Types::uuid()),
            ],
        ]);

        $config = [
            'fields' => fn (): array => [
                'createAppealCalculator' => [
                    'type' => $appealOutputType,
                    'args' => [
                        'input' => [
                            'type' => Types::nonNull(new InputObjectType([
                                'name' => 'createAppealCalculatorInput',
                                'fields' => fn (): array => [
                                    'name' => [
                                        'type' => Types::nonNull(Types::string()),
                                    ],
                                    'phone' => [
                                        'type' => Types::nonNull(Types::phoneNumber()),
                                    ],
                                    'note' => [
                                        'type' => Types::string(),
                                    ],
                                    'date' => [
                                        'type' => Types::date(),
                                    ],
                                    'equipmentId' => [
                                        'type' => Types::nonNull(Types::uuid()),
                                    ],
                                    'mileage' => [
                                        'type' => Types::nonNull(Types::int()),
                                    ],
                                    'total' => [
                                        'type' => Types::nonNull(Types::moneyInput()),
                                    ],
                                    'works' => [
                                        'type' => Types::nonNull(Types::listOf(new InputObjectType([
                                            'name' => 'CalculatorWorkInput',
                                            'fields' => fn (): array => [
                                                'id' => Types::nonNull(Types::uuid()),
                                                'name' => Types::nonNull(Types::string()),
                                                'price' => Types::nonNull(Types::moneyInput()),
                                                'type' => new EnumType([
                                                    'name' => 'CalculatorWorkType',
                                                    'values' => [
                                                        'work' => [
                                                            'value' => 'work',
                                                            'description' => 'Работа',
                                                        ],
                                                        'recommendation' => [
                                                            'value' => 'recommendation',
                                                            'description' => 'Рекомендация',
                                                        ],
                                                    ],
                                                ]),
                                                'isSelected' => [
                                                    'type' => Types::nonNull(Types::boolean()),
                                                ],
                                                'parts' => [
                                                    'type' => Types::listOf(new InputObjectType([
                                                        'name' => 'CalculatorWorkPartInput',
                                                        'fields' => fn (): array => [
                                                            'id' => Types::nonNull(Types::uuid()),
                                                            'name' => Types::nonNull(Types::string()),
                                                            'price' => Types::nonNull(Types::moneyInput()),
                                                            'count' => Types::nonNull(Types::int()),
                                                            'isSelected' => Types::nonNull(Types::boolean()),
                                                        ],
                                                    ])),
                                                ],
                                            ],
                                        ]))),
                                    ],
                                ],
                            ])),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        $appealId = AppealId::generate();
                        $context->registry->add(
                            new Calculator(
                                $appealId,
                                $args['input']['name'],
                                $args['input']['note'],
                                $args['input']['phone'],
                                $args['input']['date'] ?? null,
                                McEquipmentId::from($args['input']['equipmentId']),
                                $args['input']['mileage'],
                                $args['input']['total'],
                                $args['input']['works'],
                            ),
                        );

                        return ['appealId' => $appealId];
                    },
                ],
                'createAppealSchedule' => [
                    'type' => $appealOutputType,
                    'args' => [
                        'input' => [
                            'type' => Types::nonNull(new InputObjectType([
                                'name' => 'createAppealScheduleInput',
                                'fields' => fn () => [
                                    'name' => [
                                        'type' => Types::nonNull(Types::string()),
                                    ],
                                    'phone' => [
                                        'type' => Types::nonNull(Types::phoneNumber()),
                                    ],
                                    'date' => [
                                        'type' => Types::nonNull(Types::date()),
                                    ],
                                ],
                            ])),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        $appealId = AppealId::generate();
                        $context->registry->add(
                            new Schedule(
                                $appealId,
                                $args['input']['name'],
                                $args['input']['phone'],
                                $args['input']['date'],
                            ),
                        );

                        return ['appealId' => $appealId];
                    },
                ],
                'createAppealCooperation' => [
                    'type' => $appealOutputType,
                    'args' => [
                        'input' => [
                            'type' => Types::nonNull(new InputObjectType([
                                'name' => 'createAppealCooperationInput',
                                'fields' => fn () => [
                                    'name' => [
                                        'type' => Types::nonNull(Types::string()),
                                    ],
                                    'phone' => [
                                        'type' => Types::nonNull(Types::phoneNumber()),
                                    ],
                                ],
                            ])),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        $appealId = AppealId::generate();
                        $context->registry->add(
                            new Cooperation(
                                $appealId,
                                $args['input']['name'],
                                $args['input']['phone'],
                            ),
                        );

                        return ['appealId' => $appealId];
                    },
                ],
                'createAppealQuestion' => [
                    'type' => $appealOutputType,
                    'args' => [
                        'input' => [
                            'type' => Types::nonNull(new InputObjectType([
                                'name' => 'createAppealQuestionInput',
                                'fields' => fn () => [
                                    'name' => [
                                        'type' => Types::nonNull(Types::string()),
                                    ],
                                    'email' => [
                                        'type' => Types::nonNull(Types::email()),
                                    ],
                                    'question' => [
                                        'type' => Types::nonNull(Types::string()),
                                    ],
                                ],
                            ])),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        $appealId = AppealId::generate();
                        $context->registry->add(
                            new Question(
                                $appealId,
                                $args['input']['name'],
                                $args['input']['email'],
                                $args['input']['question'],
                            ),
                        );

                        return ['appealId' => $appealId];
                    },
                ],
                'createAppealTireFitting' => [
                    'type' => $appealOutputType,
                    'args' => [
                        'input' => [
                            'type' => Types::nonNull(new InputObjectType([
                                'name' => 'createAppealTireFittingInput',
                                'fields' => fn () => [
                                    'name' => [
                                        'type' => Types::nonNull(Types::string()),
                                    ],
                                    'phone' => [
                                        'type' => Types::nonNull(Types::phoneNumber()),
                                    ],
                                    'vehicleId' => [
                                        'type' => Types::uuid(),
                                    ],
                                    'category' => [
                                        'type' => Types::nonNull(Types::tireFittingCategory()),
                                    ],
                                    'diameter' => [
                                        'type' => Types::int(),
                                    ],
                                    'total' => [
                                        'type' => Types::nonNull(Types::moneyInput()),
                                    ],
                                    'works' => [
                                        'type' => Types::nonNull(Types::listOf(new InputObjectType([
                                            'name' => 'TireFittingWorkInput',
                                            'fields' => fn (): array => [
                                                'name' => Types::nonNull(Types::string()),
                                                'price' => Types::nonNull(Types::moneyInput()),
                                            ],
                                        ]))),
                                    ],
                                ],
                            ])),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        $appealId = AppealId::generate();
                        $context->registry->add(
                            new TireFitting(
                                $appealId,
                                $args['input']['name'],
                                $args['input']['phone'],
                                VehicleId::try($args['input']['vehicleId'] ?? null),
                                TireFittingCategory::create($args['input']['category']),
                                $args['input']['diameter'],
                                $args['input']['total'],
                                $args['input']['works'],
                            ),
                        );

                        return ['appealId' => $appealId];
                    },
                ],
                'createAppealCall' => [
                    'type' => $appealOutputType,
                    'args' => [
                        'input' => [
                            'type' => Types::nonNull(new InputObjectType([
                                'name' => 'createAppealCallInput',
                                'fields' => fn () => [
                                    'phone' => [
                                        'type' => Types::nonNull(Types::phoneNumber()),
                                    ],
                                ],
                            ])),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        $appealId = AppealId::generate();
                        $context->registry->add(
                            new Call(
                                $appealId,
                                $args['input']['phone'],
                            ),
                        );

                        return ['appealId' => $appealId];
                    },
                ],
            ],
        ];

        parent::__construct($config);
    }
}
