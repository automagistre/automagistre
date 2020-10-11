<?php

namespace App\Tests\Part\Messages;

use App\Order\Entity\OrderId;
use App\Order\Fixtures\OrderFixtures;
use App\Order\Messages\OrderClosed;
use App\Part\Entity\PartCase;
use App\Part\Fixtures\GasketFixture;
use App\Part\Messages\OrderClosedHandler;
use App\Shared\Doctrine\Registry;
use App\User\Entity\User;
use App\User\Fixtures\AdminFixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @see OrderClosedHandler
 */
final class OrderClosedHandlerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        $registry = self::$container->get(Registry::class);
        /** @var User $user */
        $user = $registry->get(User::class, AdminFixtures::ID);

        $token = new TestBrowserToken($user->getRoles(), $user);
        $token->setAuthenticated(true);
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = self::$container->get('security.token_storage');
        $tokenStorage->setToken($token);
    }

    public function test(): void
    {
        $orderId = OrderId::fromString(OrderFixtures::ID);

        /** @var OrderClosedHandler $listener */
        $listener = self::$container->get(OrderClosedHandler::class);
        $listener(new OrderClosed($orderId));

        $registry = self::$container->get(Registry::class);
        $partCase = $registry->findBy(PartCase::class, ['partId' => GasketFixture::ID]);

        static::assertNotEmpty($partCase);
    }
}
