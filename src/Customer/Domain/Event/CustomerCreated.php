<?php

namespace App\Customer\Domain\Event;

use App\Doctrine\ORM\Type\Identifier;
use Symfony\Contracts\EventDispatcher\Event;

final class CustomerCreated extends Event
{
    private Identifier $customId;

    public function __construct(Identifier $customId)
    {
        $this->customId = $customId;
    }

    public function id(): Identifier
    {
        return $this->customId;
    }
}
