<?php

declare(strict_types=1);

namespace App\Part\Event;

use App\Part\Domain\PartId;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @method PartId getSubject()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartDecreased extends GenericEvent
{
    public function __construct(PartId $partId, array $arguments = [])
    {
        parent::__construct($partId, $arguments);
    }
}
