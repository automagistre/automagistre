<?php

declare(strict_types=1);

namespace App\Note\Entity;

use App\CreatedBy\Entity\CreatedByView;
use App\Note\Enum\NoteType;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="note_view")
 *
 * @psalm-suppress MissingConstructor
 */
class NoteView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="uuid")
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
     * @ORM\Column(type="created_by_view")
     */
    public CreatedByView $created;
}
