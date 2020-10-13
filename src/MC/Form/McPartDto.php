<?php

declare(strict_types=1);

namespace App\MC\Form;

use App\MC\Entity\McLine;
use App\Part\Entity\PartId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class McPartDto
{
    /**
     * @var McLine
     *
     * @Assert\NotBlank
     * @Assert\Type(McLine::class)
     */
    public $line;

    /**
     * @var PartId
     *
     * @Assert\NotBlank
     * @Assert\Type(PartId::class)
     */
    public $partId;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Type("int")
     */
    public $quantity;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     */
    public $recommended = false;
}
