<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\MC\Part as MCPart;
use App\Entity\Landlord\Part;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOPart
{
    public bool $selected;

    public bool $recommend;

    /**
     * @Assert\NotBlank
     */
    public Part $part;

    /**
     * @Assert\NotBlank
     */
    public int $quantity = 100;

    /**
     * @Assert\NotBlank
     */
    public ?Money $price;

    public static function from(MCPart $part): self
    {
        $model = new self();
        $model->part = $part->part;
        $model->quantity = $part->quantity;
        $model->recommend = $part->recommended;
        $model->selected = !$model->recommend;
        $model->price = null;

        return $model;
    }
}
