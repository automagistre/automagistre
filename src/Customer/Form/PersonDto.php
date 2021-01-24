<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Validator\CustomerPhoneNotExists;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumberConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class PersonDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $firstName;

    /**
     * @var null|string
     */
    public $lastName;

    /**
     * @var PhoneNumber
     *
     * @Assert\NotBlank
     * @PhoneNumberConstraint
     * @CustomerPhoneNotExists
     */
    public $telephone;

    /**
     * @var null|string
     *
     * @Assert\Email
     */
    public $email;
}
