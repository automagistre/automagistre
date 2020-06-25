<?php

declare(strict_types=1);

namespace App\Order\Fixtures;

use App\Car\Entity\CarId;
use App\Car\Fixtures\Primera2004Fixtures;
use App\Customer\Entity\OperandId;
use App\Customer\Fixtures\PersonVasyaFixtures;
use App\Note\Entity\Note;
use App\Note\Enum\NoteType;
use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Order\Entity\OrderItemGroup;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\OrderPayment;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Fixtures\GasketFixture;
use App\Shared\Doctrine\Registry;
use App\State;
use App\User\Entity\User;
use App\User\Fixtures\EmployeeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1eab641c-9f5f-63a4-86d0-0242c0a8100a';
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
            EmployeeFixtures::class,
            GasketFixture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->registry->getBy(User::class, ['uuid' => EmployeeFixtures::ID]);
        $this->state->user($user);

        $orderId = OrderId::fromString(self::ID);
        $order = new Order(
            $orderId
        );
        $manager->persist($order);

        $order->setCustomerId(OperandId::fromString(self::CUSTOMER_ID));

        $order->setCarId(CarId::fromString(self::CAR_ID));

        $this->addReference('order-1', $order);
        $manager->persist(new Note($orderId->toUuid(), NoteType::info(), 'Order Note'));

        $money = new Money(100, new Currency('RUB'));

        $orderItemGroup = new OrderItemGroup($order, 'Group');
        $manager->persist($orderItemGroup);
        $manager->flush();

        $orderItemService = new OrderItemService($order, 'Service', $money);
        $manager->persist($orderItemService);
        $manager->flush();

        $partId = PartId::fromString(GasketFixture::ID);
        $orderItemPart = new OrderItemPart($order, $partId, 1);
        $orderItemPart->setPrice($money, $this->registry->get(PartView::class, $partId));

        $manager->persist(
            new OrderPayment(
                $order,
                new Money('100', new Currency('RUB')),
                null
            )
        );

        $manager->persist($orderItemPart);
        $manager->flush();
    }
}
