<?php

declare(strict_types=1);

namespace App\Tests\Order;

use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Order\Entity\OrderItemPart;
use App\Part\Entity\PartId;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OrderItemPartTest extends TestCase
{
    public function testDiscount(): void
    {
        $orderItemPart = new OrderItemPart(
            Uuid::uuid6(),
            null,
            new Order(OrderId::generate(), 1),
            PartId::generate(),
            Money::RUB(0),
            1,
            false,
        );

        $orderItemPart->setPrice(Money::RUB(1000));
        $orderItemPart->changeDiscount(Money::RUB(1500));
        self::assertDiscount($orderItemPart, Money::RUB(1500), Money::RUB(500));

        $orderItemPart->setPrice(Money::RUB(1500));
        self::assertDiscount($orderItemPart, Money::RUB(1500), Money::RUB(0));

        $orderItemPart->changeDiscount(Money::RUB(2000));
        self::assertDiscount($orderItemPart, Money::RUB(2000), Money::RUB(500));

        $orderItemPart->changeDiscount(Money::RUB(1000));
        self::assertDiscount($orderItemPart, Money::RUB(1500), Money::RUB(0));

        $orderItemPart->changeDiscount(Money::RUB(2000));
        self::assertDiscount($orderItemPart, Money::RUB(2000), Money::RUB(500));

        $orderItemPart->changeDiscount(Money::RUB(3000));
        self::assertDiscount($orderItemPart, Money::RUB(3000), Money::RUB(1500));
    }

    private static function assertDiscount(OrderItemPart $item, Money $price, Money $discount): void
    {
        $actualPrice = $item->getPrice();
        $actualDiscount = $item->discount();

        self::assertTrue(
            $actualPrice->equals($price) && $actualDiscount->equals($discount),
            sprintf(
                'Expected %s:%s, got %s:%s',
                $price->getAmount(),
                $discount->getAmount(),
                $actualPrice->getAmount(),
                $actualDiscount->getAmount(),
            ),
        );
    }
}
