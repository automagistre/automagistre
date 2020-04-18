<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Review
{
    use Identity;
    use CreatedAt;

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
        string $author,
        string $manufacturer,
        string $model,
        string $content,
        string $url,
        DateTimeImmutable $publishAt
    ) {
        $this->author = $author;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->content = $content;
        $this->source = $url;
        $this->publishAt = $publishAt;
    }
}
