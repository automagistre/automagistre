<?php

declare(strict_types=1);

namespace App\Appeal\Rest\Dto;

use App\Vehicle\Enum\BodyType;
use function array_map;
use function implode;
use function in_array;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use function sprintf;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @psalm-suppress MissingConstructor
 */
final class TireFittingDto
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
     * @var string
     *
     * @Assert\NotBlank
     */
    public $modelId;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $bodyType;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Type(type="int")
     */
    public $diameter;

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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context): void
    {
        $types = array_map(static fn (BodyType $enum): string => $enum->toName(), BodyType::all());

        if (!in_array($this->bodyType, $types, true)) {
            $context
                ->buildViolation(sprintf('Wrong body type. Available: %s', implode(',', $types)))
                ->atPath('bodyType')
                ->addViolation();
        }
    }
}
