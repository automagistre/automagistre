<?php

declare(strict_types=1);

namespace App\Entity\Superclass;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\NoteType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
abstract class Note
{
    use Identity;
    use CreatedAt;

    /**
     * @var NoteType
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="note_type_enum", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="text")
     */
    private $text;

    public function __toString(): string
    {
        return $this->getText() ?? '...';
    }

    public function getType(): ?NoteType
    {
        return $this->type;
    }

    public function setType(NoteType $type): void
    {
        $this->type = $type;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
}
