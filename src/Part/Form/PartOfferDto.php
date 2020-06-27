<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

final class PartOfferDto
{
    /**
     * @var PartId
     *
     * @Assert\NotBlank
     */
    public $partId;

    /**
     * @var int
     *
     * @Assert\NotBlank
     */
    public $quantity = 100;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $price;

    public function __construct(PartId $partId, int $quantity, Money $price)
    {
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
    }
}
