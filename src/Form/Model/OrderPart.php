<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Customer\Entity\Operand;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Part\Entity\PartId;
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
     * @var int
     *
     * @Assert\NotBlank
     */
    public $quantity = 100;

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
     * @var Operand|null
     */
    public $supplier;

    public function __construct(
        Order $order,
        ?OrderItem $parent,
        PartId $partId,
        int $quantity,
        Money $price,
        bool $warranty,
        ?Operand $supplier
    ) {
        $this->order = $order;
        $this->parent = $parent;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->warranty = $warranty;
        $this->supplier = $supplier;
    }

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
