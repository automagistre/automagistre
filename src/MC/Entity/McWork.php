<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function sprintf;

/**
 * @ORM\Entity
 */
class McWork extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
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
     * @ORM\Column(nullable=true)
     */
    public ?string $comment = null;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    public Money $price;

    public function __construct(McWorkId $id, string $name, ?string $description, Money $price, ?string $comment)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->comment = $comment;
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
