<?php

declare(strict_types=1);

namespace App\Entity;

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

    /**
     * @var User
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    public function __construct(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }

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

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }
}
