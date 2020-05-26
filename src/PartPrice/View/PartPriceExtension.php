<?php

declare(strict_types=1);

namespace App\PartPrice\View;

use App\Part\Entity\PartId;
use App\PartPrice\PartPrice;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PartPriceExtension extends AbstractExtension
{
    private PartPrice $partPrice;

    public function __construct(PartPrice $partPrice)
    {
        $this->partPrice = $partPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('part_price', fn (PartId $partId) => $this->partPrice->price($partId)),
            new TwigFunction('part_discount', fn (PartId $partId) => $this->partPrice->discount($partId)),
        ];
    }
}
