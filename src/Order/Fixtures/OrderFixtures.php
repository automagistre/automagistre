<?php

declare(strict_types=1);

namespace App\Order\Fixtures;

use App\Car\Entity\Car;
use App\Car\Infrastructure\Fixtures\Primera2004Fixtures;
use App\Customer\Domain\Operand;
use App\Doctrine\Registry;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemGroup;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\OrderNote;
use App\Enum\NoteType;
use App\Part\Domain\Part;
use App\State;
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

    private State $state;

    public function __construct(Registry $registry, State $state)
    {
        $this->registry = $registry;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->registry->manager(User::class)->getReference(User::class, 1);
        $this->state->user($user);

        $order = new Order();
        $manager->persist($order);

        $customer = $this->registry->reference(Operand::class, 1);
        $order->setCustomer($customer);

        $car = $this->registry->reference(Car::class, 2);
        $order->setCar($car);

        $this->addReference('order-1', $order);
        $manager->persist(new OrderNote($order, NoteType::info(), 'Order Note'));

        $money = new Money(100, new Currency('RUB'));
        $part = $this->registry->manager(Part::class)->getReference(Part::class, 1);

        $orderItemGroup = new OrderItemGroup($order, 'Group');
        $manager->persist($orderItemGroup);
        $manager->flush();

        $orderItemService = new OrderItemService($order, 'Service', $money);
        $manager->persist($orderItemService);
        $manager->flush();

        $orderItemPart = new OrderItemPart($order, $part, 1, $money);
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
