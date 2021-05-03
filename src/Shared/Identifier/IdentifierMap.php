<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use InvalidArgumentException;
use Premier\Identifier\Identifier;
use function array_key_exists;
use function get_class;
use function sprintf;

final class IdentifierMap
{
    private static array $map = [
        \App\Appeal\Entity\AppealId::class => \App\Appeal\Entity\AppealView::class,
        \App\Calendar\Entity\CalendarEntryId::class => \App\Calendar\Entity\CalendarEntry::class,
        \App\Car\Entity\CarId::class => \App\Car\Entity\Car::class,
        \App\Car\Entity\RecommendationId::class => \App\Car\Entity\Recommendation::class,
        \App\Car\Entity\RecommendationPartId::class => \App\Car\Entity\RecommendationPart::class,
        \App\Customer\Entity\CustomerTransactionId::class => \App\Customer\Entity\CustomerTransaction::class,
        \App\Customer\Entity\OperandId::class => \App\Customer\Entity\Operand::class,
        \App\Employee\Entity\EmployeeId::class => \App\Employee\Entity\Employee::class,
        \App\Employee\Entity\SalaryId::class => \App\Employee\Entity\Salary::class,
        \App\Expense\Entity\ExpenseId::class => \App\Expense\Entity\Expense::class,
        \App\Income\Entity\IncomeId::class => \App\Income\Entity\Income::class,
        \App\Income\Entity\IncomePartId::class => \App\Income\Entity\IncomePart::class,
        \App\Manufacturer\Entity\ManufacturerId::class => \App\Manufacturer\Entity\Manufacturer::class,
        \App\MC\Entity\McEquipmentId::class => \App\MC\Entity\McEquipment::class,
        \App\MC\Entity\McWorkId::class => \App\MC\Entity\McWork::class,
        \App\Order\Entity\OrderId::class => \App\Order\Entity\Order::class,
        \App\Part\Entity\PartCaseId::class => \App\Part\Entity\PartCase::class,
        \App\Part\Entity\PartId::class => \App\Part\Entity\Part::class,
        \App\Review\Entity\ReviewId::class => \App\Review\Entity\Review::class,
        \App\Sms\Entity\SmsId::class => \App\Sms\Entity\Sms::class,
        \App\Storage\Entity\WarehouseId::class => \App\Storage\Entity\Warehouse::class,
        \App\User\Entity\UserId::class => \App\User\Entity\User::class,
        \App\User\Entity\UserPasswordId::class => \App\User\Entity\UserPassword::class,
        \App\Vehicle\Entity\VehicleId::class => \App\Vehicle\Entity\Model::class,
        \App\Wallet\Entity\WalletId::class => \App\Wallet\Entity\Wallet::class,
        \App\Wallet\Entity\WalletTransactionId::class => \App\Wallet\Entity\WalletTransaction::class,
    ];

    /**
     * @psalm-return class-string
     */
    public function entityClassByIdentifier(Identifier $identifier): string
    {
        $class = get_class($identifier);

        if (!array_key_exists($class, self::$map)) {
            throw new InvalidArgumentException(sprintf('Not found entity class for identifier class %s', $class));
        }

        return self::$map[$class];
    }
}
