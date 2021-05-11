<?php

declare(strict_types=1);

namespace App\Storage\Form\Motion;

use App\Part\Entity\PartId;
use Symfony\Component\Validator\Constraints as Assert;

final class MotionDto
{
    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="int")
     */
    public $quantity = 0;

    /**
     * @var null|string
     *
     * @Assert\Type(type="string")
     */
    public $description;

    public function __construct(public PartId $partId)
    {
    }
}
