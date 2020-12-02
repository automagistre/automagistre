<?php

declare(strict_types=1);

namespace App\Review\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Review\Entity\Review;
use App\Review\Entity\ReviewId;
use App\Review\Form\ReviewDto;
use function assert;
use function strtolower;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ReviewController extends AbstractController
{
    protected function createNewEntity(): ReviewDto
    {
        return new ReviewDto();
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof ReviewDto);

        $entity = new Review(
            ReviewId::generate(),
            $dto->author,
            $dto->manufacturer,
            $dto->model,
            $dto->content,
            $dto->source,
            $dto->publishAt,
        );
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
