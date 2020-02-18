<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function sprintf;

/**
 * @ORM\Entity
 */
class Work
{
    use Identity;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Embedded(class="Money\Money")
     */
    public Money $price;

    public function __construct(string $name, ?string $description, Money $price)
    {
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
}
