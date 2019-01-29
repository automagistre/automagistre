<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Manager\PartManager;
use App\Manager\PriceManager;
use App\Manager\ReservationManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PartExtension extends AbstractExtension
{
    /**
     * @var PartManager
     */
    private $partManager;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    /**
     * @var PriceManager
     */
    private $priceManager;

    public function __construct(
        PartManager $partManager,
        ReservationManager $reservationManager,
        PriceManager $priceManager
    ) {
        $this->partManager = $partManager;
        $this->reservationManager = $reservationManager;
        $this->priceManager = $priceManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('part_in_stock', [$this->partManager, 'inStock']),
            new TwigFunction('part_crosses', [$this->partManager, 'getCrosses']),
            new TwigFunction('part_crosses_in_stock', [$this->partManager, 'crossesInStock']),
            new TwigFunction('part_reserved', [$this->reservationManager, 'reserved']),
            new TwigFunction('part_reservable', [$this->reservationManager, 'reservable']),
            new TwigFunction('part_suggest_price', [$this->priceManager, 'suggestForPart']),
        ];
    }
}
