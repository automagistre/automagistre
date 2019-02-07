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
    /**
     * @var bool
     */
    public $selected;

    /**
     * @var bool
     */
    public $recommend;

    /**
     * @var string
     */
    public $service;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var Money|null
     */
    public $discount;

    /**
     * @var OrderTOPart[]
     *
     * @Assert\Valid
     */
    public $parts = [];

    public static function from(Line $line): self
    {
        $model = new self();
        $model->service = $line->work->name;
        $model->price = $line->work->price;
        $model->recommend = $line->recommended;

        $model->selected = !$model->recommend;

        foreach ($line->parts as $part) {
            $model->parts[$part->getId()] = OrderTOPart::from($part);
        }

        return $model;
    }
}
