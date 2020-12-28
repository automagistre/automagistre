<?php

declare(strict_types=1);

namespace App\Fixtures\Order;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Fixtures\Car\Primera2004Fixtures;
use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Fixtures\Part\GasketFixture;
use App\Fixtures\User\UserEmployeeFixtures;
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
use App\Shared\Doctrine\Registry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

final class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1eab641c-9f5f-63a4-86d0-0242c0a8100a';
    public const NUMBER = '1';
    public const CAR_ID = Primera2004Fixtures::ID;
    public const CUSTOMER_ID = PersonVasyaFixtures::ID;

    public const GROUP_ID = '1eab7ac7-2b8a-62dc-9c38-0242c0a81005';
    public const SERVICE_ID = '1eab7ac7-c95f-6822-80e2-0242c0a81005';
    public const PART_ID = '1eab7ac7-f145-69d6-a083-0242c0a81005';

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            PersonVasyaFixtures::class,
            Primera2004Fixtures::class,
            UserEmployeeFixtures::class,
            GasketFixture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $orderId = OrderId::fromString(self::ID);
        $order = new Order(
            $orderId,
            self::NUMBER,
        );
        $manager->persist($order);

        $order->setCustomerId(OperandId::fromString(self::CUSTOMER_ID));

        $order->setCarId(CarId::fromString(self::CAR_ID));

        $manager->persist(new Note($orderId->toUuid(), NoteType::info(), 'Order Note'));

        $money = new Money(100, new Currency('RUB'));

        $orderItemGroup = new OrderItemGroup(Uuid::fromString(self::GROUP_ID), $order, 'Group');
        $manager->persist($orderItemGroup);
        $manager->flush();

        $orderItemService = new OrderItemService(Uuid::fromString(self::SERVICE_ID), $order, 'Service', $money);
        $manager->persist($orderItemService);
        $manager->flush();

        $partId = PartId::fromString(GasketFixture::ID);
        $orderItemPart = new OrderItemPart(Uuid::fromString(self::PART_ID), $order, $partId, 1);
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
