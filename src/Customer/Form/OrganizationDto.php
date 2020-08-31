<?php

declare(strict_types=1);

namespace App\Customer\Form;

use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumberConstraint;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var string|null
     *
     * @Assert\Email
     */
    public $email;

    public function __construct(string $name, PhoneNumber $telephone, string $email)
    {
        $this->name = $name;
        $this->telephone = $telephone;
        $this->email = $email;
    }
}
