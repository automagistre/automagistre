<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\MC\Part;
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
    public \App\Entity\Landlord\Part $part;

    /**
     * @Assert\NotBlank
     */
    public int $quantity;

    /**
     * @Assert\NotBlank
     */
    public Money $price;

    public static function from(Part $part): self
    {
        $model = new self();
        $model->part = $part->part;
        $model->quantity = $part->quantity;
        $model->recommend = $part->recommended;

        $model->selected = !$model->recommend;

        return $model;
    }
}
