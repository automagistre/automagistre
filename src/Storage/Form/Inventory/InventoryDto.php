<?php

declare(strict_types=1);

namespace App\Storage\Form\Inventory;

use Symfony\Component\Validator\Constraints as Assert;

final class InventoryDto
{
    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="int")
     */
    public $quantity = 0;

    /**
     * @var null|string
     *
     * @Assert\Type(type="string")
     */
    public $description;
}
