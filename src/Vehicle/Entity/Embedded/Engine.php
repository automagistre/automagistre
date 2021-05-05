<?php

declare(strict_types=1);

namespace App\Vehicle\Entity\Embedded;

use App\Vehicle\Enum\AirIntake;
use App\Vehicle\Enum\FuelType;
use App\Vehicle\Enum\Injection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use function sprintf;

/**
 * @ORM\Embeddable
 */
final class Engine
{
    private const DEFAULT_CAPACITY = '0';

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $name = null;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(type="engine_type_enum")
     */
    public FuelType $type;

    /**
     * @ORM\Column(type="engine_air_intake")
     */
    public AirIntake $airIntake;

    /**
     * @ORM\Column(type="engine_injection")
     */
    public Injection $injection;

    /**
     * @Assert\NotBlank
     * @Assert\Type("numeric")
     *
     * @ORM\Column
     */
    public string $capacity;

    public function __construct(
        string $name = null,
        FuelType $type = null,
        AirIntake $airIntake = null,
        Injection $injection = null,
        string $capacity = null,
    ) {
        $this->name = $name;
        $this->type = $type ?? FuelType::unknown();
        $this->airIntake = $airIntake ?? AirIntake::unknown();
        $this->injection = $injection ?? Injection::unknown();
        $this->capacity = $capacity ?? self::DEFAULT_CAPACITY;
    }

    public function isFilled(): bool
    {
        return
            null !== $this->name
            && !$this->type->eq(FuelType::unknown())
            && self::DEFAULT_CAPACITY !== $this->capacity;
    }

    public function toString(): string
    {
        return sprintf('%s %s', $this->name ?? '-', $this->capacity);
    }
}
