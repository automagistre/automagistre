<?php

declare(strict_types=1);

namespace App\Appeal\Rest\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class QuestionDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $question;
}
