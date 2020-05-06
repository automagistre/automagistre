<?php

namespace App\Tests\EventListener;

use App\Doctrine\Registry;
use App\Entity\Tenant\Order;
use App\EventListener\PartCaseOnOrderCloseListener;
use App\Part\Domain\PartCase;
use App\Part\Infrastructure\Fixtures\GasketFixture;
use App\State;
use App\Tenant\Tenant;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PartCaseOnOrderCloseListenerTest extends KernelTestCase
{
    public function test(): void
    {
        self::bootKernel();
        self::$container->get(State::class)->tenant(Tenant::fromIdentifier('msk'));

        $registry = self::$container->get(Registry::class);

        $order = $registry->findBy(Order::class, ['id' => '1']);
        self::$container->get(PartCaseOnOrderCloseListener::class)->onOrderClosed(new GenericEvent($order));

        $partCase = $registry->findBy(PartCase::class, ['partId' => GasketFixture::ID]);

        static::assertNotEmpty($partCase);
    }
}
