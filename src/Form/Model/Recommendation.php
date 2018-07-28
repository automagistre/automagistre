<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Car;
use App\Entity\CarRecommendation;
use App\Entity\Operand;
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
     * @var Operand
     */
    public $worker;

    public static function getEntityClass(): string
    {
        return CarRecommendation::class;
    }
}
