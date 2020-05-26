<?php

declare(strict_types=1);

namespace App\Manufacturer\Entity;

use App\Costil;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Manufacturer
{
    use Identity;

    /**
     * @ORM\Column(type="manufacturer_id")
     */
    private ManufacturerId $uuid;

    /**
     * @ORM\Column(name="name", length=64, nullable=true)
     */
    private ?string $name;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $localizedName;

    /**
     * @ORM\Column(name="logo", length=25, nullable=true)
     */
    private ?string $logo;

    public function __construct(
        ManufacturerId $id = null,
        string $name = null,
        string $localizedName = null,
        string $logo = null
    ) {
        $this->uuid = $id ?? ManufacturerId::generate();
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->logo = $logo;
    }

    public function __toString(): string
    {
        return Costil::display($this->uuid);
    }

    public function toId(): ManufacturerId
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocalizedName(): ?string
    {
        return $this->localizedName;
    }

    public function setLocalizedName(?string $localizedName): void
    {
        $this->localizedName = $localizedName;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }
}
