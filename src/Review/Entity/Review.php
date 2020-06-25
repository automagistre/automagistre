<?php

declare(strict_types=1);

namespace App\Review\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Review
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column
     */
    public string $author;

    /**
     * @ORM\Column
     */
    public string $manufacturer;

    /**
     * @ORM\Column
     */
    public string $model;

    /**
     * @ORM\Column(type="text")
     */
    public string $content;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     *
     * @ORM\Column
     */
    public string $source;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $publishAt;

    public function __construct(
        UuidInterface $id,
        string $author,
        string $manufacturer,
        string $model,
        string $content,
        string $source,
        DateTimeImmutable $publishAt
    ) {
        $this->id = $id;
        $this->author = $author;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->content = $content;
        $this->source = $source;
        $this->publishAt = $publishAt;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }
}
