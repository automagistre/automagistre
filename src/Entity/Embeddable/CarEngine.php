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
        $this->capacity = $capacity ?? '0';
    }
}
