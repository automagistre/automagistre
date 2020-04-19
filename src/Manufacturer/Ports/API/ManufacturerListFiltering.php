<?php

declare(strict_types=1);

namespace App\Manufacturer\Ports\API;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class ManufacturerListFiltering
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Choice({"id", "name"})
     */
    public $field;

    /**
     * @var string
     *
     * @Assert\Choice(
     *     choices={
     *         "=",
     *         "<>",
     *         "<",
     *         "<=",
     *         ">",
     *         ">=",
     *         "IN",
     *         "NOT IN",
     *     }
     * )
     * @Assert\NotBlank
     */
    public $comparison;

    /**
     * @var string|string[]
     *
     * @Assert\NotBlank
     */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $field, string $comparison, $value)
    {
        $this->field = $field;
        $this->comparison = $comparison;
        $this->value = $value;
    }
}
