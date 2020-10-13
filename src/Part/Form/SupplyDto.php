<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Customer\Entity\OperandId;
use App\Part\Entity\PartId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class SupplyDto
{
    /**
     * @Assert\NotBlank()
     */
    public PartId $partId;

    /**
     * @Assert\NotBlank()
     */
    public OperandId $supplierId;

    /**
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value="0")
     */
    public int $quantity;
}
