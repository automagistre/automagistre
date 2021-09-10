<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Doctrine\Registry;

final class PartViewRepository
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function get(PartId $partId): PartView
    {
        return $this->registry->get(PartView::class, $partId);
    }
}
