<?php

return [
    App\Calendar\Domain\DeletionReason::class => ['deletion_reason'],
    App\Enum\NoteType::class => ['note_type_enum'],
    App\Enum\OrderStatus::class => ['order_status_enum'],
    App\Storage\Enum\Source::class => ['motion_source_enum'],
    App\Tenant\Tenant::class => ['tenant_enum'],
    App\Vehicle\Domain\BodyType::class => ['carcase_enum'],
    App\Vehicle\Domain\DriveWheelConfiguration::class => ['car_wheel_drive_enum'],
    App\Vehicle\Domain\FuelType::class => ['engine_type_enum'],
    App\Vehicle\Domain\Transmission::class => ['car_transmission_enum'],
];
