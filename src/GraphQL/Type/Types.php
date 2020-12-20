<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\GraphQL\Type\Definition\ConnectionType;
use App\GraphQL\Type\Definition\DateType;
use App\GraphQL\Type\Definition\EmailType;
use App\GraphQL\Type\Definition\MoneyInputType;
use App\GraphQL\Type\Definition\MoneyType;
use App\GraphQL\Type\Definition\NodeType;
use App\GraphQL\Type\Definition\PageInfoType;
use App\GraphQL\Type\Definition\PhoneNumberType;
use App\GraphQL\Type\Definition\UuidType;
use App\GraphQL\Type\Definition\YearType;
use App\Manufacturer\GraphQL\Type\ManufacturerType;
use App\MC\GraphQL\Type\MaintenanceType;
use App\MC\GraphQL\Type\PartItemType;
use App\MC\GraphQL\Type\WorkType;
use App\Part\GraphQL\Type\PartType;
use App\Part\GraphQL\Type\UnitType;
use App\Review\GraphQL\Type\ReviewSourceType;
use App\Review\GraphQL\Type\ReviewType;
use App\Shared\Doctrine\Registry;
use App\Vehicle\GraphQL\Type\AirIntakeType;
use App\Vehicle\GraphQL\Type\BodyEnumType;
use App\Vehicle\GraphQL\Type\EngineType;
use App\Vehicle\GraphQL\Type\FuelType;
use App\Vehicle\GraphQL\Type\InjectionType;
use App\Vehicle\GraphQL\Type\ProductionType;
use App\Vehicle\GraphQL\Type\TransmissionType;
use App\Vehicle\GraphQL\Type\VehicleType;
use App\Vehicle\GraphQL\Type\WheelDriveType;
use function assert;
use GraphQL\Type\Definition\Type;
use function str_ends_with;
use function substr;
use function ucfirst;

/**
 * @method static AirIntakeType airIntake()
 * @method static BodyEnumType vehicleBody()
 * @method static ConnectionType connection(Type $type)
 * @method static DateType date()
 * @method static EmailType email()
 * @method static EngineType engine()
 * @method static FuelType fuel()
 * @method static InjectionType injection()
 * @method static MaintenanceType maintenance()
 * @method static ManufacturerType manufacturer()
 * @method static MoneyType money()
 * @method static MoneyInputType moneyInput()
 * @method static NodeType node()
 * @method static PageInfoType pageInfo()
 * @method static PartItemType partItem()
 * @method static PartType part()
 * @method static PhoneNumberType phoneNumber()
 * @method static ProductionType production()
 * @method static ReviewSourceType reviewSource()
 * @method static ReviewType review()
 * @method static TransmissionType transmission()
 * @method static UnitType unit()
 * @method static UuidType uuid()
 * @method static VehicleType vehicle()
 * @method static WheelDriveType wheelDrive()
 * @method static WorkType work()
 * @method static YearType year()
 */
final class Types extends Type
{
    public static Registry $registry;

    private static array $map = [
        'AirIntake' => AirIntakeType::class,
        'Connection' => ConnectionType::class,
        'Date' => DateType::class,
        'Email' => EmailType::class,
        'Engine' => EngineType::class,
        'Fuel' => FuelType::class,
        'Injection' => InjectionType::class,
        'Maintenance' => MaintenanceType::class,
        'Manufacturer' => ManufacturerType::class,
        'Money' => MoneyType::class,
        'MoneyInput' => MoneyInputType::class,
        'Node' => NodeType::class,
        'PageInfo' => PageInfoType::class,
        'Part' => PartType::class,
        'PartItem' => PartItemType::class,
        'PhoneNumber' => PhoneNumberType::class,
        'Production' => ProductionType::class,
        'Review' => ReviewType::class,
        'ReviewSource' => ReviewSourceType::class,
        'Transmission' => TransmissionType::class,
        'Unit' => UnitType::class,
        'Uuid' => UuidType::class,
        'Vehicle' => VehicleType::class,
        'VehicleBody' => BodyEnumType::class,
        'WheelDrive' => WheelDriveType::class,
        'Work' => WorkType::class,
        'Year' => YearType::class,
    ];

    /**
     * @var array<string, Type>
     */
    private static array $instances = [];

    public static function __callStatic(string $name, array $args = []): Type
    {
        $name = ucfirst($name);

        return self::$instances[$name] ??= self::get($name, $args);
    }

    private static function get(string $name, array $args): Type
    {
        $isConnection = 'Connection' !== $name && str_ends_with($name, 'Connection');
        if ($isConnection) {
            $name = substr($name, 0, -10);
        }

        $type = new self::$map[$name](...$args);

        assert($type instanceof Type);

        return $isConnection ? self::connection($type) : $type;
    }
}
