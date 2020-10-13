<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class PartOfferDto
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
     */
    public $quantity = 100;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $price;
}
