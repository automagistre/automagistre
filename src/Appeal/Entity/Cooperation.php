<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_cooperation")
 */
class Cooperation
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

    public function __construct(UuidInterface $id, string $name, PhoneNumber $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
    }

    public static function create(string $name, PhoneNumber $phone): self
    {
        return new self(
            Uuid::uuid6(),
            $name,
            $phone,
        );
    }
}
