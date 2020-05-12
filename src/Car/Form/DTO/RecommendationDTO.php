<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Customer\Domain\OperandId;
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

    public ?OperandId $workerId = null;

    public function __construct(Car $car, string $service = null, Money $price = null, OperandId $workerId = null)
    {
        $this->car = $car;
        $this->service = $service;
        $this->price = $price;
        $this->workerId = $workerId;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityClass(): string
    {
        return Recommendation::class;
    }
}
