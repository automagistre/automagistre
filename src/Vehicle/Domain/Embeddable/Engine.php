<?php

declare(strict_types=1);

namespace App\Vehicle\Domain\Embeddable;

use App\Vehicle\Enum\AirIntake;
use App\Vehicle\Enum\FuelType;
use App\Vehicle\Enum\Injection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ORM\Mapping as ORM;
use function sprintf;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 * @ODM\EmbeddedDocument
 */
final class Engine
{
    private const DEFAULT_CAPACITY = '0';

    /**
     * @ORM\Column(nullable=true)
     * @ODM\Field(nullable=true)
     */
    public ?string $name = null;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(type="engine_type_enum")
     * @ODM\Field(type="engine_type_enum")
     */
    public FuelType $type;

    /**
     * @ORM\Column(type="engine_air_intake", nullable=true)
     * @ODM\Field(type="engine_air_intake", nullable=true)
     */
    public ?AirIntake $airIntake;

    /**
     * @ORM\Column(type="engine_injection", nullable=true)
     * @ODM\Field(type="engine_injection", nullable=true)
     */
    public ?Injection $injection;

    /**
     * @Assert\NotBlank
     * @Assert\Type("numeric")
     *
     * @ORM\Column
     * @ODM\Field()
     */
    public string $capacity;

    public function __construct(
        string $name = null,
        FuelType $type = null,
        AirIntake $airIntake = null,
        Injection $injection = null,
        string $capacity = null
    ) {
        $this->name = $name;
        $this->type = $type ?? FuelType::unknown();
        $this->airIntake = $airIntake;
        $this->injection = $injection;
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
        return sprintf('%s %s', $this->name, $this->capacity);
    }
}
