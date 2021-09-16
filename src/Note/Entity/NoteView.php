<?php

declare(strict_types=1);

namespace App\Note\Entity;

use App\CreatedBy\Entity\Blamable;
use App\Note\Enum\NoteType;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="note_view")
 *
 * @psalm-suppress MissingConstructor
 */
class NoteView extends TenantEntity
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
     * @ORM\Embedded(class=Blamable::class)
     */
    public Blamable $created;
}
