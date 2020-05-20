<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Customer\Validator\CustomerPhoneNotExists;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumberConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
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
     * @var string|null
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

    public function __construct(string $firstName, ?string $lastName, PhoneNumber $telephone)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->telephone = clone $telephone;
    }
}
