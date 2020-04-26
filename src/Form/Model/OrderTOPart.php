<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\MC\Part as MCPart;
use App\Part\Domain\Part;
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
    public Part $part;

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

    public function __construct(Part $part, int $quantity, Money $price, bool $selected, bool $recommend)
    {
        $this->part = $part;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->selected = $selected;
        $this->recommend = $recommend;
    }

    public static function from(MCPart $part): self
    {
        return new self($part->part, $part->quantity, $part->part->getPrice(), $part->recommended, !$part->recommended);
    }
}
