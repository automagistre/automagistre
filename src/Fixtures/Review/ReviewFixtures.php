<?php

declare(strict_types=1);

namespace App\Fixtures\Review;

use App\Review\Entity\Review;
use App\Review\Entity\ReviewId;
use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
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
        $identifier = ReviewId::fromString(self::ID);
        $review = new Review(
            $identifier,
            $identifier->toString(),
            ReviewSource::manual(),
            'Onotole',
            'Zaibatsu',
            ReviewRating::unspecified(),
            new DateTimeImmutable('2019-12-25 23:51'),
            [
                'manufacturer' => 'Nissan',
                'model' => 'GTR',
                'source' => 'http://reviews.club/item/1',
            ]
        );
        $review->eraseMessages();

        $manager->persist($review);
        $manager->flush();
    }
}
