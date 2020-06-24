<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Part\Entity\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOPart
{
    /**
     * @Assert\NotBlank
     */
    public PartId $partId;

    /**
     * @Assert\NotBlank
     */
    public int $quantity;

    /**
     * @Assert\NotBlank
     */
    public Money $price;

    public bool $selected;

    public bool $recommend;

    public function __construct(PartId $partId, int $quantity, Money $price, bool $selected, bool $recommend)
    {
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->selected = $selected;
        $this->recommend = $recommend;
    }
}
