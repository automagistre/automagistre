<?php

declare(strict_types=1);

namespace App\Tests\Part\Messages;

use App\Doctrine\Registry;
use App\Fixtures\Employee\AdminFixtures;
use App\Fixtures\Order\OrderFixtures;
use App\Fixtures\Part\GasketFixture;
use App\Order\Entity\OrderId;
use App\Order\Messages\OrderDealed;
use App\Part\Entity\PartCase;
use App\Part\Messages\LinkPartCaseOnOrderClosedListener;
use App\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @see LinkPartCaseOnOrderClosedListener
 */
final class LinkPartCaseOnOrderClosedListenerTest extends KernelTestCase
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
        $orderId = OrderId::from(OrderFixtures::ID);

        /** @var LinkPartCaseOnOrderClosedListener $listener */
        $listener = self::$container->get(LinkPartCaseOnOrderClosedListener::class);
        $listener(new OrderDealed($orderId));

        $registry = self::$container->get(Registry::class);
        $partCase = $registry->findOneBy(PartCase::class, ['partId' => GasketFixture::ID]);

        self::assertNotEmpty($partCase);
    }
}
