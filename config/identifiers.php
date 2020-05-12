<?php

return [
    App\Calendar\Domain\CalendarEntryId::class => ['calendar_entry_id'],
    App\Car\Entity\CarId::class => ['car_id'],
    App\Car\Entity\RecommendationId::class => ['recommendation_id'],
    App\Car\Entity\RecommendationPartId::class => ['recommendation_part_id'],
    App\Customer\Domain\OperandId::class => ['operand_id'],
    App\Income\Entity\IncomeId::class => ['income_id'],
    App\Manufacturer\Domain\ManufacturerId::class => ['manufacturer_id'],
    App\Order\Entity\OrderId::class => ['order_id'],
    App\Part\Domain\PartId::class => ['part_id'],
    App\Storage\Entity\MotionId::class => ['motion_id'],
    App\User\Domain\UserId::class => ['user_id'],
    App\Vehicle\Domain\VehicleId::class => ['vehicle_id'],
];
