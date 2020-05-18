<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserCredentials
{
    use Identity;
    use CreatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="credentials")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $identifier;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $payloads;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expiredAt;

    public function __construct(User $user, string $type, string $identifier, array $payloads = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->identifier = $identifier;
        $this->payloads = $payloads;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPayloads(): array
    {
        return $this->payloads;
    }

    public function getExpiredAt(): ?DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function expire(): void
    {
        $this->expiredAt = new DateTimeImmutable();
    }
}
