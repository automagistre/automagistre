<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\MC\Line;
use App\Entity\Landlord\MC\Part;
use function assert;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

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

    public function __construct(string $service, Money $price, array $parts, bool $selected, bool $recommend)
    {
        $this->service = $service;
        $this->price = $price;
        $this->parts = $parts;
        $this->selected = $selected;
        $this->recommend = $recommend;
    }

    public static function from(Line $line): self
    {
        $work = $line->work;

        $parts = [];
        foreach ($line->parts as $part) {
            assert($part instanceof Part);

            $parts[(int) $part->getId()] = OrderTOPart::from($part);
        }

        return new self($work->name, $work->price, $parts, !$line->recommended, $line->recommended);
    }
}
