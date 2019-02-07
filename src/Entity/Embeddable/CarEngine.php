<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Enum\EngineType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
final class CarEngine
{
    private const DEFAULT_CAPACITY = '0';

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(nullable=true)
     */
    public $name;

    /**
     * @var EngineType
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="engine_type_enum")
     */
    public $type;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    public $capacity;

    public function __construct(string $name = null, EngineType $type = null, string $capacity = null)
    {
        $this->name = $name;
        $this->type = $type ?? EngineType::unknown();
        $this->capacity = $capacity ?? self::DEFAULT_CAPACITY;
    }

    public function isFilled(): bool
    {
        return
            null !== $this->name
            && !$this->type->eq(EngineType::unknown())
            && self::DEFAULT_CAPACITY !== $this->capacity;
    }

    public function toString(): string
    {
        return \sprintf('%s %s', $this->name, $this->capacity);
    }
}
