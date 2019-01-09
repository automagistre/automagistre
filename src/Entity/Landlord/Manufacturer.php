<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Manufacturer
{
    use Identity;
    use Uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $localizedName;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", length=25, nullable=true)
     */
    private $logo;

    public function __construct(string $name = '')
    {
        $this->generateUuid();
        $this->name = $name;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
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
