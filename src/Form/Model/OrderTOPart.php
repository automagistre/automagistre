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
    /**
     * @var bool
     */
    public $selected;

    /**
     * @var bool
     */
    public $recommend;

    /**
     * @var \App\Entity\Landlord\Part
     *
     * @Assert\NotBlank
     */
    public $part;

    /**
     * @var int
     *
     * @Assert\NotBlank
     */
    public $quantity;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $price;

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
