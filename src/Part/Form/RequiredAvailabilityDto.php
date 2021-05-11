<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
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
     * @Assert\GreaterThanOrEqual(value="0")
     * @Assert\AtLeastOneOf(
     *     constraints={
     *       @Assert\Expression(expression="this.orderUpToQuantity === 0"),
     *       @Assert\Expression(expression="this.orderUpToQuantity > this.orderFromQuantity"),
     *     },
     *     includeInternalMessages=false,
     *     message="Значение должно быть больше остатка."
     * )
     */
    public $orderUpToQuantity;
}
