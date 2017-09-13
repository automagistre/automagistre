<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Price;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class Service
{
    use Price;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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

    public function getId(): int
    {
        return $this->id;
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
