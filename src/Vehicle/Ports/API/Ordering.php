<?php

declare(strict_types=1);

namespace App\Vehicle\Ports\API;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class Ordering
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Choice({"id", "name", "localized_name"})
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
