<?php

declare(strict_types=1);

return [
    App\Appeal\Enum\AppealStatus::class => ['appeal_status'],
    App\Appeal\Enum\AppealType::class => ['appeal_type'],
    App\Calendar\Enum\DeletionReason::class => ['deletion_reason'],
    App\Customer\Enum\CustomerTransactionSource::class => ['operand_transaction_source'],
    App\Note\Enum\NoteType::class => ['note_type_enum'],
    App\Order\Enum\OrderSatisfaction::class => ['order_satisfaction_enum'],
    App\Order\Enum\OrderStatus::class => ['order_status_enum'],
    App\Part\Enum\SupplySource::class => ['part_supply_source_enum'],
    App\Part\Enum\Unit::class => ['unit_enum'],
    App\Review\Enum\ReviewSource::class => ['review_source'],
    App\Review\Enum\ReviewRating::class => ['review_star_rating'],
    App\Shared\Enum\Transition::class => ['transition_enum'],
    App\Storage\Enum\MotionType::class => ['motion_source_enum'],
    App\Tenant\Enum\Tenant::class => ['tenant_enum'],
    App\Vehicle\Enum\AirIntake::class => ['engine_air_intake'],
    App\Vehicle\Enum\BodyType::class => ['carcase_enum'],
    App\Vehicle\Enum\DriveWheelConfiguration::class => ['car_wheel_drive_enum'],
    App\Vehicle\Enum\FuelType::class => ['engine_type_enum'],
    App\Vehicle\Enum\Injection::class => ['engine_injection'],
    App\Vehicle\Enum\TireFittingCategory::class => ['tire_fitting_category'],
    App\Vehicle\Enum\Transmission::class => ['car_transmission_enum'],
    App\Wallet\Enum\WalletTransactionSource::class => ['wallet_transaction_source'],
];
