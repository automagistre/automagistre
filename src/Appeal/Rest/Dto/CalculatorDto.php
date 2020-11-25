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
     * @var string|null
     */
    public $note;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(allowNull=true)
     * @Assert\Date
     */
    public $date;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $equipmentId;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Type(type="int")
     */
    public $mileage;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Type(type="int")
     */
    public $total;

    /**
     * @var array
     *
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $works;
}
