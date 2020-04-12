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
     * @Assert\NotBlank()
     */
    public string $field;

    /**
     * @Assert\Choice(choices={"asc", "desc"})
     * @Assert\NotBlank()
     */
    public string $direction;

    private function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }
}
