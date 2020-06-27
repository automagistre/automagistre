<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Customer\Entity\OperandId;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Part\Form\PartOfferDto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderPart extends OrderItemModel
{
    /**
     * @var PartOfferDto
     *
     * @Assert\Valid
     * @Assert\NotBlank
     */
    public $partOffer;

    /**
     * @var bool
     */
    public $warranty = false;

    /**
     * @var OperandId|null
     */
    public $supplierId;

    public function __construct(
        Order $order,
        ?OrderItem $parent,
        PartOfferDto $partOffer,
        bool $warranty,
        ?OperandId $supplier
    ) {
        $this->order = $order;
        $this->parent = $parent;
        $this->partOffer = $partOffer;
        $this->warranty = $warranty;
        $this->supplierId = $supplier;
    }

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
