<?php

declare(strict_types=1);

namespace App\Manufacturer\Ports\API;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class Paging
{
    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotBlank
     */
    public $page = 1;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotBlank
     */
    public $size = 50;
}
