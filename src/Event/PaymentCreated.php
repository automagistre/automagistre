<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Tenant\Transaction;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @method Transaction getSubject()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentCreated extends GenericEvent
{
    public function __construct(Transaction $subject, array $arguments = [])
    {
        parent::__construct($subject, $arguments);
    }
}
