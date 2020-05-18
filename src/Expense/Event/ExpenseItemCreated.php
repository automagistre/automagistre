<?php

declare(strict_types=1);

namespace App\Expense\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExpenseItemCreated extends GenericEvent
{
}
