<?php

declare(strict_types=1);

namespace App\Order\Fixtures;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemGroup;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\OrderNote;
use App\Enum\NoteType;
use App\User\Entity\User;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class OrderFixtures extends Fixture implements FixtureGroupInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->registry->manager(User::class)->getReference(User::class, 1);

        $order = new Order();
        $order->setCreatedBy($user);
        $manager->persist($order);

        $this->addReference('order-1', $order);
        $manager->persist(new OrderNote($order, $user, NoteType::info(), 'Order Note'));

        $money = new Money(100, new Currency('RUB'));
        $part = $this->registry->manager(Part::class)->getReference(Part::class, 1);

        $orderItemGroup = new OrderItemGroup($order, 'Group', $user);
        $manager->persist($orderItemGroup);
        $manager->flush();

        $orderItemService = new OrderItemService($order, 'Service', $money, $user);
        $manager->persist($orderItemService);
        $manager->flush();

        $orderItemPart = new OrderItemPart($order, $part, 1, $money, $user);
        $manager->persist($orderItemPart);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }
}
