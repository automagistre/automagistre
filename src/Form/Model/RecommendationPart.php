<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\CarRecommendationPart;
use App\Entity\Landlord\Part;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationPart extends Model
{
    /**
     * @Assert\NotBlank
     */
    public CarRecommendation $recommendation;

    /**
     * @Assert\NotBlank
     */
    public ?Part $part;

    /**
     * @Assert\NotBlank
     */
    public int $quantity = 100;

    /**
     * @Assert\NotBlank
     */
    public ?Money $price;

    public function __construct()
    {
        $this->part = null;
        $this->price = null;
    }

    public static function getEntityClass(): string
    {
        return CarRecommendationPart::class;
    }
}
