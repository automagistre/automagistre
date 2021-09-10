<?php

declare(strict_types=1);

namespace App\Tests\Part\Messages;

use App\Doctrine\Registry;
use App\Fixtures\Order\OrderFixtures;
use App\Fixtures\Part\GasketFixture;
use App\Order\Entity\OrderId;
use App\Order\Messages\OrderDealed;
use App\Part\Entity\PartCase;
use App\Part\Messages\LinkPartCaseOnOrderClosedListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\InMemoryUser;

/**
 * @see LinkPartCaseOnOrderClosedListener
 */
final class LinkPartCaseOnOrderClosedListenerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $container = self::getContainer();

        $token = new TestBrowserToken(
            ['ROLE_ADMIN'],
            new InMemoryUser(username: '1ea9478c-eca4-6f96-a221-3ab8c77b35e5', password: 'pa$$word'),
        );
        $token->setAuthenticated(true);
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $container->get('security.token_storage');
        $tokenStorage->setToken($token);
    }

    public function test(): void
    {
        $container = self::getContainer();
        $orderId = OrderId::from(OrderFixtures::ID);

        /** @var LinkPartCaseOnOrderClosedListener $listener */
        $listener = $container->get(LinkPartCaseOnOrderClosedListener::class);
        $listener(new OrderDealed($orderId));

        $registry = $container->get(Registry::class);
        $partCase = $registry->findOneBy(PartCase::class, ['partId' => GasketFixture::ID]);

        self::assertNotEmpty($partCase);
    }
}
