<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Operand;
use App\Entity\OrderItemService;
use App\Entity\Service;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderService extends OrderItemModel
{
    /**
     * @var Service
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

    public static function getEntityClass(): string
    {
        return OrderItemService::class;
    }
}
