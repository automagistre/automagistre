<?php

declare(strict_types=1);

namespace App\Storage\Form\Inventorization;

use App\Part\Entity\PartId;
use App\Storage\Entity\InventorizationId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class InventorizationPartDto
{
    #[Assert\NotBlank]
    public PartId $partId;

    #[Assert\NotBlank]
    public int $quantity;

    public function __construct(public InventorizationId $inventorizationId)
    {
    }
}
