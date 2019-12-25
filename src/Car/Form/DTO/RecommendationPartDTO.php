<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Entity\Landlord\Part;
use App\Form\Model\Model;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationPartDTO extends Model
{
    /**
     * @Assert\NotBlank
     */
    public Recommendation $recommendation;

    /**
     * @Assert\NotBlank
     */
    public ?Part $part = null;

    /**
     * @Assert\NotBlank
     */
    public int $quantity = 100;

    /**
     * @Assert\NotBlank
     */
    public ?Money $price = null;

    /**
     * {@inheritdoc}
     */
    public static function getEntityClass(): string
    {
        return RecommendationPart::class;
    }
}
