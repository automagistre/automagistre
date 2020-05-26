<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Form\Model\Model;
use App\Part\Entity\PartId;
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
    public ?PartId $partId = null;

    /**
     * @Assert\NotBlank
     */
    public int $quantity = 100;

    /**
     * @Assert\NotBlank
     */
    public ?Money $price = null;

    public function __construct(
        Recommendation $recommendation,
        PartId $partId = null,
        int $quantity = 100,
        Money $price = null
    ) {
        $this->recommendation = $recommendation;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityClass(): string
    {
        return RecommendationPart::class;
    }
}
