<?php

declare(strict_types=1);

namespace App\Review\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Review\Entity\Review;
use function assert;
use function strtolower;

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
        assert($entity instanceof Review);

        $this->normalize($entity);

        parent::persistEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        assert($entity instanceof Review);

        $this->normalize($entity);

        parent::updateEntity($entity);
    }

    private function normalize(Review $review): void
    {
        $review->manufacturer = strtolower($review->manufacturer);
    }
}
