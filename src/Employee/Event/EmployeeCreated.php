<?php

declare(strict_types=1);

namespace App\Employee\Event;

use App\Employee\Entity\EmployeeId;
use App\MessageBus\Async;

/**
 * @psalm-immutable
 */
final class EmployeeCreated implements Async
{
    public function __construct(public EmployeeId $employeeId)
    {
    }
}
