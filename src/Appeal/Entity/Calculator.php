<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\MC\Entity\McEquipmentId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_calculator")
 */
class Calculator
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
     * @ORM\Column(nullable=true)
     */
    public ?string $note;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    /**
     * @ORM\Column(type="date_immutable")
     */
    public DateTimeImmutable $date;

    /**
     * @ORM\Column(type="mc_equipment_id")
     */
    public McEquipmentId $equipmentId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $mileage;

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
        ?string $note,
        PhoneNumber $phone,
        DateTimeImmutable $date,
        McEquipmentId $equipmentId,
        int $mileage,
        Money $total,
        array $works
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->note = $note;
        $this->phone = $phone;
        $this->date = $date;
        $this->equipmentId = $equipmentId;
        $this->mileage = $mileage;
        $this->total = $total;
        $this->works = $works;
    }

    public static function create(
        string $name,
        ?string $note,
        PhoneNumber $phone,
        DateTimeImmutable $date,
        McEquipmentId $equipmentId,
        int $mileage,
        Money $total,
        array $works
    ): self {
        return new self(
            Uuid::uuid6(),
            $name,
            $note,
            $phone,
            $date,
            $equipmentId,
            $mileage,
            $total,
            $works,
        );
    }
}
