<?php

declare(strict_types=1);

namespace App\Note\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class NoteDelete extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Note::class, inversedBy="delete")
     */
    private Note $note;

    /**
     * @ORM\Column
     */
    private string $description;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(Note $note, string $description)
    {
        $this->id = Uuid::uuid6();
        $this->note = $note;
        $this->description = $description;
    }
}
