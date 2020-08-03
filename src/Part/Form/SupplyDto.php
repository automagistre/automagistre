<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Customer\Entity\OperandId;
use App\Part\Entity\PartId;
use Symfony\Component\Validator\Constraints as Assert;

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

    public function __construct(PartId $partId, OperandId $supplierId, int $quantity)
    {
        $this->partId = $partId;
        $this->supplierId = $supplierId;
        $this->quantity = $quantity;
    }
}
