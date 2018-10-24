<?php

declare(strict_types=1);

namespace App\Entity\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Part;
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
     * @var Part
     *
     * @ORM\Column
     */
    public $description;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    public $price;

    public function __toString()
    {
        return \sprintf('%s (%s)', $this->name, $this->description);
    }
}
