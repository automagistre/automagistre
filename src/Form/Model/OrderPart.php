<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Embeddable\OperandRelation;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\OrderItemPart;
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

    /**
     * @var Money|null
     */
    public $discount;

    /**
     * @var OperandRelation
     */
    public $supplier;

    /**
     * @var bool
     */
    public $hidden = false;

    public function __construct()
    {
        $this->supplier = new OperandRelation();
    }

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
