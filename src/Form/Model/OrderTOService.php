<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\MC\Line;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOService
{
    public bool $selected;

    public bool $recommend;

    public string $service;

    public Money $price;

    /**
     * @var OrderTOPart[]
     *
     * @Assert\Valid
     */
    public array $parts = [];

    public static function from(Line $line): self
    {
        $model = new self();

        $work = $line->work;
        $model->service = $work->name;
        $model->price = $work->price;
        $model->recommend = $line->recommended;
        $model->selected = !$model->recommend;

        foreach ($line->parts as $part) {
            $model->parts[(int) $part->getId()] = OrderTOPart::from($part);
        }

        return $model;
    }
}
