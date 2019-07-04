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
    /**
     * @var string
     */
    public $service;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var Operand
     */
    public $worker;

    /**
     * @var bool
     */
    public $warranty;

    /**
     * @var Money|null
     */
    public $discount;

    /**
     * @var bool
     */
    public $hidden = false;

    public static function getEntityClass(): string
    {
        return OrderItemService::class;
    }
}
