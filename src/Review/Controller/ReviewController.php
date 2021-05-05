<?php

declare(strict_types=1);

namespace App\Review\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Review\Entity\Review;
use App\Review\Entity\ReviewId;
use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
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
            ReviewSource::manual(),
            $dto->author,
            $dto->content,
            ReviewRating::unspecified(),
            $dto->publishAt,
            [
                'manufacturer' => $dto->manufacturer,
                'model' => $dto->model,
                'source' => $dto->source,
            ],
        );

        parent::persistEntity($entity);
    }
}
