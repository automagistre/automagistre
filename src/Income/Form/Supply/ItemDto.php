<?php

declare(strict_types=1);

namespace App\Income\Form\Supply;

use App\Part\Entity\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

final class ItemDto
{
    /**
     * @var int
     */
    #[Assert\NotBlank]
    public $quantity = 0;

    /**
     * @var Money
     */
    #[Assert\NotBlank]
    public $price;

    public function __construct(public PartId $partId)
    {
        $this->price = Money::RUB(0);
    }
}
