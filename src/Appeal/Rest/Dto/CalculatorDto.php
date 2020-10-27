<?php

declare(strict_types=1);

namespace App\Appeal\Rest\Dto;

use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class CalculatorDto
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
     * @PhoneNumber
     */
    public $phone;

    /**
     * @var array
     *
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $body;
}
