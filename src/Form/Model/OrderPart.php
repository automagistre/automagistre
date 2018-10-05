<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\OrderItemPart;
use App\Entity\Part;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderPart extends OrderItemModel
{
    /**
     * @var Part
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

    /**
     * @var bool
     */
    public $warranty = false;

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
