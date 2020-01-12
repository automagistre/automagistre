<?php

declare(strict_types=1);

namespace App\Appointment\Fixtures;

use App\Appointment\Entity\Appointment;
use App\Entity\Tenant\Order;
use App\Order\Fixtures\OrderFixtures;
use function assert;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class AppointmentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            OrderFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $order = $this->getReference('order-1');
        assert($order instanceof Order);

        $appointment = new Appointment();
        $appointment->order = $order;
        $appointment->date = new DateTimeImmutable('10:30');
        $appointment->duration = new DateInterval('PT1H');

        $manager->persist($appointment);
        $manager->flush();
    }
}
