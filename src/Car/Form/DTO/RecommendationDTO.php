<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Entity\Landlord\Operand;
use App\Form\Model\Model;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationDTO extends Model
{
    public Car $car;

    public ?string $service = null;

    public ?Money $price = null;

    public ?Operand $worker = null;

    /**
     * {@inheritdoc}
     */
    public static function getEntityClass(): string
    {
        return Recommendation::class;
    }
}
