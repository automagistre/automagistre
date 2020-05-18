<?php

declare(strict_types=1);

namespace App\Customer\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonCreated extends GenericEvent
{
}
