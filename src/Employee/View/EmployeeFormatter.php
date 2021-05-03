<?php

declare(strict_types=1);

namespace App\Employee\View;

use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class EmployeeFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return EmployeeId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $employee = $this->registry->get(Employee::class, $identifier);

        return $formatter->format($employee->toPersonId());
    }
}
