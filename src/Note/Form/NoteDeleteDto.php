<?php

declare(strict_types=1);

namespace App\Note\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
class NoteDeleteDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $description;
}
