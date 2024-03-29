<?php

declare(strict_types=1);

namespace App\Review\Google\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="google_review_token")
 */
class Token extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="json")
     */
    public array $payload;

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
}
