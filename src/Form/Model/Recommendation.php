<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\Operand;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Recommendation extends Model
{
    public Car $car;

    public string $service;

    public Money $price;

    public ?Operand $worker;

    public static function getEntityClass(): string
    {
        return CarRecommendation::class;
    }
}
