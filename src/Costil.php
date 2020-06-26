<?php

declare(strict_types=1);

namespace App;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Employee\Entity\Salary;
use App\Employee\Entity\SalaryId;
use App\Expense\Entity\Expense;
use App\Expense\Entity\ExpenseId;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Sms\Entity\Sms;
use App\Sms\Entity\SmsId;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseView;
use App\User\Entity\User;
use App\User\Entity\UserId;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletId;
use function array_key_exists;
use Money\Currency;
use Money\Money;
use function str_contains;
use function str_replace;

/**
 * Сборник костылей.
 */
final class Costil
{
    public const PODSTANOVA = 45;
    public const OLD_USER = '4ffc24e2-8e60-42e0-9c8f-7a73888b2da6';
    public const SERVICE_USER = '59861141-83b2-416c-b672-8ba8a1cb76b2';
    public const ENTITY = [
        CalendarEntryId::class => CalendarEntry::class,
        CarId::class => Car::class,
        CustomerTransactionId::class => CustomerTransaction::class,
        EmployeeId::class => Employee::class,
        ExpenseId::class => Expense::class,
        IncomeId::class => Income::class,
        IncomePartId::class => IncomePart::class,
        ManufacturerId::class => Manufacturer::class,
        OperandId::class => Operand::class,
        OrderId::class => Order::class,
        PartId::class => Part::class,
        SmsId::class => Sms::class,
        SalaryId::class => Salary::class,
        UserId::class => User::class,
        VehicleId::class => Model::class,
        WalletId::class => Wallet::class,
        WarehouseId::class => WarehouseView::class,
    ];
    public const UUID_FIELDS = [
        CalendarEntryId::class => 'id',
        CarId::class => 'uuid',
        CustomerTransactionId::class => 'id',
        EmployeeId::class => 'uuid',
        ExpenseId::class => 'uuid',
        IncomeId::class => 'id',
        IncomePartId::class => 'uuid',
        ManufacturerId::class => 'id',
        OperandId::class => 'uuid',
        OrderId::class => 'uuid',
        PartId::class => 'id',
        SmsId::class => 'id',
        UserId::class => 'uuid',
        VehicleId::class => 'uuid',
        WalletId::class => 'id',
        WarehouseId::class => 'id',
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

    public static function convertToMoney(array $array): array
    {
        foreach ($array as $key => $value) {
            if (!str_contains($key, 'currency.code')) {
                continue;
            }

            $moneyKey = str_replace('.currency.code', '', $key);
            if (!array_key_exists($moneyKey.'.amount', $array)) {
                continue;
            }

            $array[$moneyKey] = null !== $array[$moneyKey.'.amount']
                ? new Money(
                    $array[$moneyKey.'.amount'],
                    new Currency($array[$moneyKey.'.currency.code']),
                )
                : null;

            unset($array[$moneyKey.'.amount'], $array[$moneyKey.'.currency.code']);
        }

        return $array;
    }
}
