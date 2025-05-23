<?php

declare(strict_types=1);

namespace App\Manufacturer\Entity;

use App\Costil;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class Manufacturer
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public ManufacturerId $id;

    /**
     * @ORM\Column(name="name", length=64, unique=true, nullable=false)
     */
    public ?string $name;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $localizedName;

    /**
     * @ORM\Column(name="logo", length=25, nullable=true)
     */
    private ?string $logo;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(
        ManufacturerId $id = null,
        string $name = null,
        string $localizedName = null,
        string $logo = null,
    ) {
        $this->id = $id ?? ManufacturerId::generate();
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->logo = $logo;
    }

    public function __toString(): string
    {
        return Costil::display($this->id);
    }

    public function toId(): ManufacturerId
    {
        return $this->id;
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
