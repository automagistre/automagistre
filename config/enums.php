<?php

return [
    App\Calendar\Enum\DeletionReason::class => ['deletion_reason', null],
    App\Customer\Enum\CustomerTransactionSource::class => ['operand_transaction_source', null],
    App\Note\Enum\NoteType::class => ['note_type_enum', null],
    App\Order\Enum\OrderStatus::class => ['order_status_enum', null],
    App\Part\Enum\SupplySource::class => ['part_supply_source_enum', null],
    App\Part\Enum\Unit::class => ['unit_enum', null],
    App\Shared\Enum\Transition::class => ['transition_enum', null],
    App\Storage\Enum\Source::class => ['motion_source_enum', null],
    App\Tenant\Tenant::class => ['tenant_enum', null],
    App\Vehicle\Enum\AirIntake::class => ['engine_air_intake', 'name'],
    App\Vehicle\Enum\BodyType::class => ['carcase_enum', 'name'],
    App\Vehicle\Enum\DriveWheelConfiguration::class => ['car_wheel_drive_enum', 'code'],
    App\Vehicle\Enum\FuelType::class => ['engine_type_enum', 'name'],
    App\Vehicle\Enum\Injection::class => ['engine_injection', 'name'],
    App\Vehicle\Enum\Transmission::class => ['car_transmission_enum', 'code'],
    App\Wallet\Enum\WalletTransactionSource::class => ['wallet_transaction_source', null],
];
