<?php

declare(strict_types=1);

namespace App\Note\Form;

use App\Note\Enum\NoteType;
use Symfony\Component\Validator\Constraints as Assert;

final class NoteDto
{
    /**
     * @var NoteType
     *
     * @Assert\NotBlank
     */
    public $type;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $text;

    private function __construct(NoteType $type, string $text)
    {
        $this->type = $type;
        $this->text = $text;
    }
}
