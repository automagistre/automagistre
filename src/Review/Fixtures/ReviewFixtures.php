<?php

declare(strict_types=1);

namespace App\Review\Fixtures;

use App\Car\Entity\Model;
use App\Entity\Landlord\Review;
use App\Manufacturer\Entity\Manufacturer;
use function assert;
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
        $review = new Review();
        $review->manufacturer = 'Nissan';
        $review->model = 'GTR';
        $review->content = 'Zaibatsu';
        $review->author = 'Uzver';
        $review->publishAt = new DateTimeImmutable('2019-12-25 23:51');
        $review->url = 'http://reviews.club/item/1';

        $this->addReference('review-1', $review);

        $manager->persist($review);
        $manager->flush();
    }
}
