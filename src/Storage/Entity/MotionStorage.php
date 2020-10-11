<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use App\Shared\Doctrine\Registry;

final class MotionStorage
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getPart(PartId $partId): MotionalPart
    {
        return new MotionalPart($partId, $this->registry->manager());
    }
}
