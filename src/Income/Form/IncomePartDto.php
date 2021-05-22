<?php

declare(strict_types=1);

namespace App\Income\Form;

use App\Income\Entity\IncomeId;
use App\Part\Entity\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class IncomePartDto
{
    /**
     * @var PartId
     */
    #[Assert\NotBlank]
    public $partId;

    /**
     * @var Money
     */
    #[Assert\NotBlank]
    public $price;

    /**
     * @var int
     */
    #[Assert\NotBlank]
    public $quantity;

    public function __construct(public IncomeId $incomeId)
    {
    }
}
