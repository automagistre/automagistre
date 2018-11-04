<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Review;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReviewController extends AbstractController
{
    /**
     * @param Review $entity
     */
    protected function persistEntity($entity): void
    {
        $this->normalize($entity);

        parent::persistEntity($entity);
    }

    /**
     * @param Review $entity
     */
    protected function updateEntity($entity): void
    {
        $this->normalize($entity);

        parent::updateEntity($entity);
    }

    private function normalize(Review $review): void
    {
        $review->manufacturer = \strtolower($review->manufacturer);
    }
}
