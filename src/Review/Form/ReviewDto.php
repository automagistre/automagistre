<?php

declare(strict_types=1);

namespace App\Review\Form;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

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

    public function __construct(
        string $author,
        string $manufacturer,
        string $model,
        string $content,
        string $source,
        DateTimeImmutable $publishAt
    ) {
        $this->author = $author;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->content = $content;
        $this->source = $source;
        $this->publishAt = $publishAt;
    }
}
