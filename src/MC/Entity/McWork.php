<?php

declare(strict_types=1);

namespace App\MC\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function sprintf;

/**
 * @ORM\Entity
 */
class McWork
{
    /**
     * @ORM\Id
     * @ORM\Column(type="mc_work_id")
     */
    public McWorkId $id;

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

    public function __construct(McWorkId $id, string $name, ?string $description, Money $price)
    {
        $this->id = $id;
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

    public function toId(): McWorkId
    {
        return $this->id;
    }
}
