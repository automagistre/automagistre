<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Customer\Domain\Operand;
use App\Order\Entity\OrderItemService;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderService extends OrderItemModel
{
    public ?string $service;

    public ?Money $price;

    public ?Operand $worker;

    public bool $warranty;

    public ?Money $discount;

    public function __construct()
    {
        parent::__construct();

        $this->service = null;
        $this->price = null;
        $this->warranty = false;
        $this->discount = null;
    }

    public static function getEntityClass(): string
    {
        return OrderItemService::class;
    }
}
