<?php

return [
    App\Calendar\Enum\DeletionReason::class => ['deletion_reason'],
    App\Customer\Enum\CustomerTransactionSource::class => ['operand_transaction_source'],
    App\Note\Enum\NoteType::class => ['note_type_enum'],
    App\Order\Enum\OrderSatisfaction::class => ['order_satisfaction_enum'],
    App\Order\Enum\OrderStatus::class => ['order_status_enum'],
    App\Part\Enum\SupplySource::class => ['part_supply_source_enum'],
    App\Part\Enum\Unit::class => ['unit_enum'],
    App\Shared\Enum\Transition::class => ['transition_enum'],
    App\Storage\Enum\Source::class => ['motion_source_enum'],
    App\Tenant\Tenant::class => ['tenant_enum'],
    App\Vehicle\Enum\AirIntake::class => ['engine_air_intake'],
    App\Vehicle\Enum\BodyType::class => ['carcase_enum'],
    App\Vehicle\Enum\DriveWheelConfiguration::class => ['car_wheel_drive_enum'],
    App\Vehicle\Enum\FuelType::class => ['engine_type_enum'],
    App\Vehicle\Enum\Injection::class => ['engine_injection'],
    App\Vehicle\Enum\Transmission::class => ['car_transmission_enum'],
    App\Wallet\Enum\WalletTransactionSource::class => ['wallet_transaction_source'],
];
