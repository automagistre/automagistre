<?php

declare(strict_types=1);

namespace App\Income\Form;

use App\Customer\Entity\OperandId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class IncomeDto
{
    /**
     * @var OperandId
     *
     * @Assert\NotBlank
     */
    public $supplierId;

    /**
     * @var string|null
     */
    public $document;

    public function __construct(OperandId $supplierId, ?string $document)
    {
        $this->supplierId = $supplierId;
        $this->document = $document;
    }
}
