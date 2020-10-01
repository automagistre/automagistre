<?php

declare(strict_types=1);

namespace App\Manufacturer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class ManufacturerView
{
    /**
     * @ORM\Column(type="manufacturer_id")
     */
    public ManufacturerId $id;

    /**
     * @ORM\Column(name="name")
     */
    public string $name;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $localizedName;

    public function __construct(ManufacturerId $id, string $name, ?string $localizedName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->localizedName = $localizedName;
    }

    public function toId(): ManufacturerId
    {
        return $this->id;
    }
}
