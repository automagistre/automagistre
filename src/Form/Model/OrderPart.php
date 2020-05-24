<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Customer\Domain\Operand;
use App\Order\Entity\OrderItemPart;
use App\Part\Domain\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderPart extends OrderItemModel
{
    /**
     * @var PartId
     *
     * @Assert\NotBlank
     */
    public $partId;

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
