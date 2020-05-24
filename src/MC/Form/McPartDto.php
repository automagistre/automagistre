<?php

declare(strict_types=1);

namespace App\MC\Form;

use App\MC\Entity\McLine;
use App\Part\Domain\PartId;
use Symfony\Component\Validator\Constraints as Assert;

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

    public function __construct(McLine $line, PartId $partId, int $quantity, bool $recommended)
    {
        $this->line = $line;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }
}
