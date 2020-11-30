<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_schedule")
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    /**
     * @ORM\Column(type="date_immutable")
     */
    public DateTimeImmutable $date;

    public function __construct(UuidInterface $id, string $name, PhoneNumber $phone, DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->date = $date;
    }

    public static function create(string $name, PhoneNumber $phone, DateTimeImmutable $date): self
    {
        return new self(
            Uuid::uuid6(),
            $name,
            $phone,
            $date,
        );
    }
}
