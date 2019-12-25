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
    public ?string $name = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Embedded(class="Money\Money")
     */
    public ?Money $price = null;

    public function __toString(): string
    {
        $string = $this->name;

        if (null !== $this->description) {
            $string .= sprintf(' (%s)', $this->description);
        }

        return $string ?? '';
    }
}
