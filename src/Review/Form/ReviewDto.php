<?php

declare(strict_types=1);

namespace App\Review\Form;

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
    public $manufacturer;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $model;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $content;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    public $source;

    /**
     * @var DateTimeImmutable
     *
     * @Assert\NotBlank
     */
    public $publishAt;
}
