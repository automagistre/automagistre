<?php

declare(strict_types=1);

namespace App\Review\Fixtures;

use App\Review\Entity\Review;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class ReviewFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['landlord'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $review = new Review('Uzver', 'Nissan', 'GTR', 'Zaibatsu', 'http://reviews.club/item/1', new DateTimeImmutable('2019-12-25 23:51'));

        $this->addReference('review-1', $review);

        $manager->persist($review);
        $manager->flush();
    }
}
