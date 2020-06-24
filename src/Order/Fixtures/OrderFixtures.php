<?php

declare(strict_types=1);

namespace App\Order\Fixtures;

use App\Car\Entity\CarId;
use App\Car\Fixtures\Primera2004Fixtures;
use App\Customer\Entity\OperandId;
use App\Customer\Fixtures\PersonVasyaFixtures;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemGroup;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\OrderNote;
use App\Part\Entity\PartId;
use App\Part\Fixtures\GasketFixture;
use App\PartPrice\PartPrice;
use App\Shared\Doctrine\Registry;
use App\Shared\Enum\NoteType;
use App\State;
use App\User\Entity\User;
use App\User\Fixtures\EmployeeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use function dump;
use Money\Currency;
use Money\Money;

final class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const CAR_ID = Primera2004Fixtures::ID;
    public const CUSTOMER_ID = PersonVasyaFixtures::ID;

    private Registry $registry;

    private State $state;

    private PartPrice $partPrice;

    public function __construct(Registry $registry, State $state, PartPrice $partPrice)
    {
        $this->registry = $registry;
        $this->state = $state;
        $this->partPrice = $partPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            PersonVasyaFixtures::class,
            Primera2004Fixtures::class,
            EmployeeFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->registry->getBy(User::class, ['uuid' => EmployeeFixtures::ID]);
        $this->state->user($user);

        $order = new Order();
        $manager->persist($order);

        $order->setCustomerId(OperandId::fromString(self::CUSTOMER_ID));

        $order->setCarId(CarId::fromString(self::CAR_ID));

        $this->addReference('order-1', $order);
        $manager->persist(new OrderNote($order, NoteType::info(), 'Order Note'));

        $money = new Money(100, new Currency('RUB'));

        $orderItemGroup = new OrderItemGroup($order, 'Group');
        $manager->persist($orderItemGroup);
        $manager->flush();

        $orderItemService = new OrderItemService($order, 'Service', $money);
        $manager->persist($orderItemService);
        $manager->flush();

        $orderItemPart = new OrderItemPart($order, PartId::fromString(GasketFixture::ID), 1);
        $orderItemPart->setPrice($money, $this->partPrice);

        $manager->persist($orderItemPart);
        $manager->flush();
    }
}
