<?php

declare(strict_types=1);

namespace App\Order\Fixtures;

use App\Car\Entity\CarId;
use App\Car\Infrastructure\Fixtures\Primera2004Fixtures;
use App\Customer\Domain\OperandId;
use App\Customer\Infrastructure\Fixtures\PersonVasyaFixtures;
use App\Doctrine\Registry;
use App\Enum\NoteType;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemGroup;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\OrderNote;
use App\Part\Domain\Part;
use App\Part\Infrastructure\Fixtures\GasketFixture;
use App\State;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class OrderFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const CAR_ID = Primera2004Fixtures::ID;
    public const CUSTOMER_ID = PersonVasyaFixtures::ID;

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
    public function getDependencies(): array
    {
        return [
            PersonVasyaFixtures::class,
            Primera2004Fixtures::class,
        ];
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

        $order->setCustomerId(OperandId::fromString(self::CUSTOMER_ID));

        $order->setCarId(CarId::fromString(self::CAR_ID));

        $this->addReference('order-1', $order);
        $manager->persist(new OrderNote($order, NoteType::info(), 'Order Note'));

        $money = new Money(100, new Currency('RUB'));
        $part = $this->registry->findBy(Part::class, ['partId' => GasketFixture::ID]);

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
