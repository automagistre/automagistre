<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Validator\CustomerPhoneNotExists;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumberConstraint;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @var string|null
     *
     * @Assert\Email
     */
    public $email;

    public function __construct(string $firstName, ?string $lastName, PhoneNumber $telephone, ?string $email)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->telephone = $telephone;
        $this->email = $email;
    }
}
