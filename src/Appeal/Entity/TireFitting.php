<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Enum\BodyType;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_tire_fitting")
 */
class TireFitting
{
    /**
     * @ORM\Id()
     * @ORM\Column()
     */
    public UuidInterface $id;

    /**
     * @ORM\Column()
     */
    public string $name;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    /**
     * @ORM\Column(type="vehicle_id")
     */
    public VehicleId $modelId;

    /**
     * @ORM\Column(type="carcase_enum", nullable=true)
     */
    public ?BodyType $bodyType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public ?int $diameter;

    /**
     * @ORM\Column(type="money")
     */
    public Money $total;

    /**
     * @ORM\Column(type="json")
     */
    public array $works;

    public function __construct(
        UuidInterface $id,
        string $name,
        PhoneNumber $phone,
        VehicleId $modelId,
        ?BodyType $bodyType,
        ?int $diameter,
        Money $total,
        array $works
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->modelId = $modelId;
        $this->bodyType = $bodyType;
        $this->diameter = $diameter;
        $this->total = $total;
        $this->works = $works;
    }

    public static function create(
        string $name,
        PhoneNumber $phone,
        VehicleId $modelId,
        ?BodyType $bodyType,
        ?int $diameter,
        Money $total,
        array $works
    ): self {
        return new self(
            Uuid::uuid6(),
            $name,
            $phone,
            $modelId,
            $bodyType,
            $diameter,
            $total,
            $works,
        );
    }
}
