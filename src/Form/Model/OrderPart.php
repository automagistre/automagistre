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
    public ?Part $part = null;

    /**
     * @Assert\NotBlank
     */
    public int $quantity = 100;

    /**
     * @Assert\NotBlank
     */
    public ?Money $price = null;

    public bool $warranty = false;

    public ?Money $discount = null;

    public OperandRelation $supplier;

    public function __construct()
    {
        parent::__construct();

        $this->supplier = new OperandRelation();
    }

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
