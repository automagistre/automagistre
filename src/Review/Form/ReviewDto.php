<?php

declare(strict_types=1);

namespace App\Review\Form;

use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class ReviewDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $author;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $text;

    /**
     * @var ReviewSource
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    public $source;

    /**
     * @var ReviewRating
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    public $rating;

    /**
     * @var DateTimeImmutable
     *
     * @Assert\NotBlank
     */
    public $publishAt;

    public function __construct()
    {
        $this->publishAt = new DateTimeImmutable();
    }
}
