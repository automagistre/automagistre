<?php

declare(strict_types=1);

namespace App\Fixtures\Review;

use App\Review\Entity\Review;
use App\Review\Entity\ReviewId;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ReviewFixtures extends Fixture
{
    public const ID = '1eab71ba-d56d-65c6-8656-0242c0a8100a';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $review = new Review(
            ReviewId::fromString(self::ID),
            'Uzver',
            'Nissan',
            'GTR',
            'Zaibatsu',
            'http://reviews.club/item/1',
            new DateTimeImmutable('2019-12-25 23:51')
        );

        $manager->persist($review);
        $manager->flush();
    }
}
