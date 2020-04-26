<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Customer\Domain\Operand;
use App\Entity\Tenant\OrderItemPart;
use App\Part\Domain\Part;
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

    public ?Operand $supplier = null;

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
