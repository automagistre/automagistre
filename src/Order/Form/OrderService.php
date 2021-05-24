<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Customer\Entity\OperandId;
use App\Order\Entity\OrderItemService;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class OrderService extends OrderItemModel
{
    /**
     * @var string
     */
    #[Assert\NotBlank]
    public $service;

    /**
     * @var Money
     */
    #[Assert\NotBlank]
    public $price;

    public ?OperandId $workerId = null;

    public bool $warranty = false;

    public ?Money $discount = null;

    public static function getEntityClass(): string
    {
        return OrderItemService::class;
    }
}
