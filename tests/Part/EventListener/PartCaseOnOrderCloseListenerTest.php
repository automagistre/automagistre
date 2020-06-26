<?php

namespace App\Tests\Part\EventListener;

use App\Order\Entity\Order;
use App\Order\Event\OrderClosed;
use App\Order\Fixtures\OrderFixtures;
use App\Part\Entity\PartCase;
use App\Part\EventListener\PartCaseOnOrderCloseListener;
use App\Part\Fixtures\GasketFixture;
use App\Shared\Doctrine\Registry;
use function assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PartCaseOnOrderCloseListenerTest extends KernelTestCase
{
    public function test(): void
    {
        self::bootKernel();

        $registry = self::$container->get(Registry::class);

        $order = $registry->findBy(Order::class, ['id' => OrderFixtures::ID]);
        assert($order instanceof Order);
        self::$container->get(PartCaseOnOrderCloseListener::class)->onOrderClosed(new OrderClosed($order));

        $partCase = $registry->findBy(PartCase::class, ['partId' => GasketFixture::ID]);

        static::assertNotEmpty($partCase);
    }
}
