<?php

declare(strict_types=1);

namespace App;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Customer\Domain\Operand;
use App\Customer\Domain\OperandId;
use App\Doctrine\ORM\Type\Identifier;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Infrastructure\Identifier\IdentifierFormatter;
use App\Manufacturer\Domain\Manufacturer;
use App\Manufacturer\Domain\ManufacturerId;
use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Part\Domain\Part;
use App\Part\Domain\PartId;
use App\User\Domain\UserId;
use App\User\Entity\User;
use App\Vehicle\Domain\Model;
use App\Vehicle\Domain\VehicleId;

/**
 * Сборник костылей.
 */
final class Costil
{
    public const PODSTANOVA = 45;

    public const ENTITY = [
        CalendarEntryId::class => CalendarEntry::class,
        CarId::class => Car::class,
        EmployeeId::class => Employee::class,
        IncomeId::class => Income::class,
        IncomePartId::class => IncomePart::class,
        ManufacturerId::class => Manufacturer::class,
        OperandId::class => Operand::class,
        OrderId::class => Order::class,
        PartId::class => Part::class,
        UserId::class => User::class,
        VehicleId::class => Model::class,
    ];
    public const EASYADMIN_CONFIG = [
        CalendarEntryId::class => 'CalendarEntry',
        CarId::class => 'Car',
        EmployeeId::class => 'Employee',
        IncomeId::class => 'Income',
        IncomePartId::class => 'IncomePart',
        ManufacturerId::class => 'Manufacturer',
        OperandId::class => 'Operand',
        OrderId::class => 'Order',
        PartId::class => 'Part',
        UserId::class => 'User',
        VehicleId::class => 'CarModel',
    ];
    public const UUID_FIELDS = [
        CalendarEntryId::class => 'id',
        CarId::class => 'uuid',
        EmployeeId::class => 'uuid',
        IncomeId::class => 'id',
        IncomePartId::class => 'uuid',
        ManufacturerId::class => 'uuid',
        OperandId::class => 'uuid',
        OrderId::class => 'uuid',
        PartId::class => 'partId',
        UserId::class => 'uuid',
        VehicleId::class => 'uuid',
    ];
    public const QUERY = [
        CarId::class => 'car_id',
        IncomeId::class => 'income_id',
        IncomePartId::class => 'income_part_id',
        ManufacturerId::class => 'manufacturer_id',
        OperandId::class => 'operand_id',
        OrderId::class => 'order_id',
        PartId::class => 'part_id',
        UserId::class => 'user_id',
        VehicleId::class => 'vehicle_id',
    ];

    public static IdentifierFormatter $formatter;

    private function __construct()
    {
    }

    /**
     * Monkey migration. EasyAdminAutocompleteType require entity with __toString.
     */
    public static function display(Identifier $identifier, string $format = null): string
    {
        return self::$formatter->format($identifier, $format);
    }
}
