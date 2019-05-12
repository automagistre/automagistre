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
    /**
     * @var Car
     */
    public $car;

    /**
     * @var string
     */
    public $service;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var Operand|null
     */
    public $worker;

    public static function getEntityClass(): string
    {
        return CarRecommendation::class;
    }
}
