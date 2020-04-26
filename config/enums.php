<?php

return [
    App\Calendar\Domain\DeletionReason::class => ['deletion_reason'],
    App\Car\Enum\BodyType::class => ['carcase_enum'],
    App\Car\Enum\DriveWheelConfiguration::class => ['car_wheel_drive_enum'],
    App\Car\Enum\FuelType::class => ['engine_type_enum'],
    App\Car\Enum\Transmission::class => ['car_transmission_enum'],
    App\Enum\NoteType::class => ['note_type_enum'],
    App\Enum\OrderStatus::class => ['order_status_enum'],
    App\Tenant\Tenant::class => ['tenant_enum'],
];
