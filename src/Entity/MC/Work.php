<?php

declare(strict_types=1);

namespace App\Entity\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class Work
{
    use Identity;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    public $description;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    public $price;

    public function __toString(): string
    {
        $string = $this->name;

        if (null !== $this->description) {
            $string .= \sprintf(' (%s)', $this->description);
        }

        return $string;
    }
}
