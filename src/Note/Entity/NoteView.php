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

    private function __construct(
        UuidInterface $id,
        UuidInterface $subject,
        NoteType $type,
        string $text,
        CreatedByView $created
    ) {
        $this->id = $id;
        $this->subject = $subject;
        $this->type = $type;
        $this->text = $text;
        $this->created = $created;
    }

    public static function sql(): string
    {
        return '
            CREATE VIEW note_view AS
            SELECT note.id,
                note.subject,
                note.text,
                note.type,
                CONCAT_WS(
                    \';\',
                    cb.id,
                    CONCAT_WS(
                        \',\',
                        u.id,
                        u.username,
                        COALESCE(u.last_name, \'\'),
                        COALESCE(u.first_name, \'\')
                    ),
                    cb.created_at
                ) AS created
            FROM note
                    JOIN created_by cb ON cb.id = note.id
                    JOIN users u ON u.id = cb.user_id
        ';
    }
}
