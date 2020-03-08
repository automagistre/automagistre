<?php

namespace App\Customer\Domain\Event;

use App\Doctrine\ORM\Type\CustomId;
use Symfony\Contracts\EventDispatcher\Event;

final class CustomerCreated extends Event
{
    private CustomId $customId;

    public function __construct(CustomId $customId)
    {
        $this->customId = $customId;
    }

    public function id(): CustomId
    {
        return $this->customId;
    }
}
