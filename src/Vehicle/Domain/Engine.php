<?php

declare(strict_types=1);

namespace App\Vehicle\Domain;

use Doctrine\ORM\Mapping as ORM;
use function sprintf;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
final class Engine
{
    private const DEFAULT_CAPACITY = '0';

    /**
     * @Assert\NotBlank
     *
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
     * @Assert\NotBlank
     * @Assert\Type("numeric")
     *
     * @ORM\Column
     */
    public string $capacity;

    public function __construct(string $name = null, FuelType $type = null, string $capacity = null)
    {
        $this->name = $name;
        $this->type = $type ?? FuelType::unknown();
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
