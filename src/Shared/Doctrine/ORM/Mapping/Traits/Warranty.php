<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Mapping\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait Warranty
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $warranty = false;

    public function isWarranty(): bool
    {
        return $this->warranty;
    }

    public function setWarranty(bool $guarantee): void
    {
        $this->warranty = $guarantee;
    }
}
