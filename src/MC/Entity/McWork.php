<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function sprintf;

/**
 * @ORM\Entity
 */
class McWork
{
    use Identity;

    /**
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $uuid;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    public Money $price;

    public function __construct(string $name, ?string $description, Money $price)
    {
        $this->uuid = Uuid::uuid6();
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public function __toString(): string
    {
        $string = $this->name;

        if (null !== $this->description) {
            $string .= sprintf(' (%s)', $this->description);
        }

        return $string;
    }

    public function toId(): UuidInterface
    {
        return $this->uuid;
    }
}
