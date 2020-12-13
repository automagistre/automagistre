<?php

declare(strict_types=1);

namespace App\Publish\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class PublishDto
{
    /**
     * @var string
     *
     * @Assert\Uuid
     * @Assert\NotBlank
     */
    public $id;

    /**
     * @var bool
     *
     * @Assert\AtLeastOneOf({
     *     @Assert\IsTrue,
     *     @Assert\IsFalse
     * })
     */
    public $publish = false;
}
