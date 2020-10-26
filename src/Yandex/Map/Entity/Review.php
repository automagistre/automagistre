<?php

declare(strict_types=1);

namespace App\Yandex\Map\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="yandex_map_review")
 */
class Review
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column()
     */
    public string $reviewId;

    /**
     * @ORM\Column(type="json")
     */
    public array $payload;

    public function __construct(UuidInterface $id, string $reviewId, array $payload)
    {
        $this->id = $id;
        $this->reviewId = $reviewId;
        $this->payload = $payload;
    }

    public static function create(string $reviewId, array $payload): self
    {
        return new self(
            Uuid::uuid6(),
            $reviewId,
            $payload,
        );
    }
}
