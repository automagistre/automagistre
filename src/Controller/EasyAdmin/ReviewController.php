<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Review;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReviewController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        \assert($entity instanceof Review);

        $this->normalize($entity);

        parent::persistEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        \assert($entity instanceof Review);

        $this->normalize($entity);

        parent::updateEntity($entity);
    }

    private function normalize(Review $review): void
    {
        $review->manufacturer = \strtolower($review->manufacturer);
    }
}
