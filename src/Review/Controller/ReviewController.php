<?php

declare(strict_types=1);

namespace App\Review\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Review\Entity\Review;
use App\Review\Entity\ReviewId;
use App\Review\Form\ReviewDto;
use function assert;

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

        $reviewId = ReviewId::generate();
        $entity = new Review(
            $reviewId,
            $reviewId->toString(),
            $dto->source,
            $dto->author,
            $dto->text,
            $dto->rating,
            $dto->publishAt,
            [],
        );

        parent::persistEntity($entity);
    }
}
