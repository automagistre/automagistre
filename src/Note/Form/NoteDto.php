<?php

declare(strict_types=1);

namespace App\Note\Form;

use App\Note\Enum\NoteType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
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
}
