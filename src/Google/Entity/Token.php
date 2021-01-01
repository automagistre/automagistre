<?php

declare(strict_types=1);

namespace App\Google\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="google_review_token")
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="json")
     */
    public array $payload;

    /**
     * @ORM\OneToOne(targetEntity=TokenExpire::class)
     */
    public ?TokenExpire $expire = null;

    public function __construct(UuidInterface $id, array $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    public static function create(array $payload): self
    {
        return new self(
            Uuid::uuid6(),
            $payload,
        );
    }

    public function expire(): void
    {
        $this->expire = new TokenExpire(
            Uuid::uuid6(),
            $this,
        );
    }
}
