<?php

declare(strict_types=1);

namespace App\Manufacturer\Ports\API;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class ManufacturerListOrdering
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Choice(
     *     choices={"name", "localizedName"},
     *     message="Неверное значение {{ value }}. Доступные значения: name, localizedName"
     * )
     */
    public $field;

    /**
     * @var string
     *
     * @Assert\Choice(choices={"asc", "desc"})
     * @Assert\NotBlank
     */
    public $direction;

    private function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }
}
