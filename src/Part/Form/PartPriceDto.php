<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use DateTimeImmutable;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class PartPriceDto
{
    /**
     * @var PartId
     *
     * @Assert\NotBlank
     */
    public $partId;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $price;

    /**
     * @Assert\NotBlank()
     */
    public DateTimeImmutable $since;
}
