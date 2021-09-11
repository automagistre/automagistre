<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Validator\CustomerPhoneNotExists;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumberConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @psalm-suppress MissingConstructor
 */
final class PersonDto
{
    /**
     * @var null|string
     */
    public $firstname;

    /**
     * @var null|string
     */
    public $lastname;

    /**
     * @var null|PhoneNumber
     *
     * @PhoneNumberConstraint
     * @CustomerPhoneNotExists
     */
    public $telephone;

    /**
     * @var null|PhoneNumber
     *
     * @PhoneNumberConstraint
     */
    public $officePhone;

    /**
     * @var null|string
     *
     * @Assert\Email
     */
    public $email;

    /**
     * @var bool
     */
    public $contractor = false;

    /**
     * @var bool
     */
    public $seller = false;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (null === $this->firstname && null === $this->lastname) {
            $context->buildViolation('Нужно ввести либо имя либо фамилию')
                ->atPath('firstname')
                ->addViolation()
            ;
        }

        if (null === $this->telephone && null === $this->officePhone && null === $this->email) {
            $context->buildViolation('Нужно ввести либо телефон либо E-Mail')
                ->atPath('telephone')
                ->addViolation()
            ;
        }
    }
}
