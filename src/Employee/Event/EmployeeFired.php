<?php

declare(strict_types=1);

namespace App\Employee\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeFired extends GenericEvent
{
}
