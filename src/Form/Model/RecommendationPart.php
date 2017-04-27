<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\CarRecommendation;
use App\Entity\CarRecommendationPart;
use App\Entity\Part;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationPart extends Model
{
    /**
     * @var CarRecommendation
     */
    public $recommendation;

    /**
     * @var Part
     */
    public $part;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var Money
     */
    public $price;

    public static function getEntityClass(): string
    {
        return CarRecommendationPart::class;
    }
}
