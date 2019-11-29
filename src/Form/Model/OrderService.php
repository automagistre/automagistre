<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\Operand;
use App\Entity\Tenant\OrderItemService;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderService extends OrderItemModel
{
    public string $service;

    public Money $price;

    public Operand $worker;

    public bool $warranty;

    public ?Money $discount;

    public static function getEntityClass(): string
    {
        return OrderItemService::class;
    }
}
