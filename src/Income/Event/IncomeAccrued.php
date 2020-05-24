<?php

declare(strict_types=1);

namespace App\Income\Event;

use App\Income\Entity\Income;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @method Income getSubject()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeAccrued extends GenericEvent
{
    public function __construct(Income $income, array $arguments = [])
    {
        parent::__construct($income, $arguments);
    }
}
