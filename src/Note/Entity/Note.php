<?php

declare(strict_types=1);

namespace App\Note\Entity;

use App\Note\Enum\NoteType;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $subject;

    /**
     * @ORM\Column(type="note_type_enum")
     */
    private NoteType $type;

    /**
     * @ORM\Column(type="text")
     */
    private string $text;

    public function __construct(UuidInterface $subject, NoteType $type, string $text)
    {
        $this->id = Uuid::uuid6();
        $this->subject = $subject;
        $this->type = $type;
        $this->text = $text;
    }
}
