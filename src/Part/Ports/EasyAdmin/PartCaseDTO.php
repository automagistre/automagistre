<?php

declare(strict_types=1);

namespace App\Part\Ports\EasyAdmin;

use App\Part\Domain\Part;
use App\Vehicle\Domain\Model;
use Symfony\Component\Validator\Constraints as Assert;

final class PartCaseDTO
{
    /**
     * @var Part
     *
     * @Assert\NotBlank
     */
    public $part;

    /**
     * @var Model
     *
     * @Assert\NotBlank
     */
    public $vehicle;

    public function __construct(Part $part = null, Model $vehicle = null)
    {
        $this->part = $part;
        $this->vehicle = $vehicle;
    }
}
