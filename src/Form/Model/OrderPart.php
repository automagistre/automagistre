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
     * @Assert\NotBlank
     */
    public ?Part $part;

    /**
     * @Assert\NotBlank
     */
    public int $quantity = 100;

    /**
     * @Assert\NotBlank
     */
    public ?Money $price;

    public bool $warranty = false;

    public ?Money $discount;

    public ?OperandRelation $supplier;

    public function __construct()
    {
        parent::__construct();

        $this->part = null;
        $this->price = null;
        $this->discount = null;
        $this->supplier = null;
    }

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
