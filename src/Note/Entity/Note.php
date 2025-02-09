<?php

declare(strict_types=1);

namespace App\Note\Entity;

use App\Note\Enum\NoteType;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class Note extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\Column
     */
    public UuidInterface $subject;

    /**
     * @ORM\Column(type="note_type_enum")
     */
    public NoteType $type;

    /**
     * @ORM\Column(type="text")
     */
    public string $text;

    /**
     * @ORM\OneToOne(targetEntity=NoteDelete::class, mappedBy="note", cascade={"persist"})
     */
    private ?NoteDelete $delete = null;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $subject, NoteType $type, string $text)
    {
        $this->id = Uuid::uuid6();
        $this->subject = $subject;
        $this->type = $type;
        $this->text = $text;
    }

    public function delete(string $description): void
    {
        $this->delete = new NoteDelete($this, $description);
    }
}
