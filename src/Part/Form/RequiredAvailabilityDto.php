<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use Symfony\Component\Validator\Constraints as Assert;

final class RequiredAvailabilityDto
{
    /**
     * @var PartId
     *
     * @Assert\NotBlank
     */
    public $partId;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value="0")
     */
    public $orderFromQuantity;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\GreaterThan(value="0")
     */
    public $orderUpToQuantity;

    public function __construct(PartId $partId, int $orderFromQuantity, int $orderUpToQuantity)
    {
        $this->partId = $partId;
        $this->orderFromQuantity = $orderFromQuantity;
        $this->orderUpToQuantity = $orderUpToQuantity;
    }
}
