<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Customer\Entity\OperandId;
use App\Order\Entity\OrderItemPart;
use App\Part\Form\PartOfferDto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
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

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
