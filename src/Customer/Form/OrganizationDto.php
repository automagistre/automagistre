<?php

declare(strict_types=1);

namespace App\Customer\Form;

use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumberConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class OrganizationDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var PhoneNumber
     *
     * @PhoneNumberConstraint
     */
    public $telephone;

    /**
     * @var null|string
     *
     * @Assert\Email
     */
    public $email;
}
