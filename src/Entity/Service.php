<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class Service
{
    use Identity;
    use Price;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     */
    private $name;

    public function __construct(string $name, Money $price)
    {
        $this->name = $name;
        $this->changePrice($price);
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPrice(Money $price): void
    {
        $this->changePrice($price);
    }
}
