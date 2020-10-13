<?php

declare(strict_types=1);

namespace App\Income\Form;

use App\Customer\Entity\OperandId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
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
}
