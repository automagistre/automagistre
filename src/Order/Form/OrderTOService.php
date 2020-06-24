<?php

declare(strict_types=1);

namespace App\Order\Form;

use function iterator_to_array;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Traversable;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOService
{
    public string $service;

    public Money $price;

    /**
     * @var OrderTOPart[]
     *
     * @Assert\Valid
     */
    public array $parts = [];

    public bool $selected;

    public bool $recommend;

    public function __construct(string $service, Money $price, Traversable $parts, bool $selected, bool $recommend)
    {
        $this->service = $service;
        $this->price = $price;
        $this->parts = iterator_to_array($parts);
        $this->selected = $selected;
        $this->recommend = $recommend;
    }
}
