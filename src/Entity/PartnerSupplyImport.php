<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PartnerSupplyImport
{
    use Identity;
    use CreatedAt;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $externalId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct(string $externalId, \DateTime $date)
    {
        $this->externalId = $externalId;
        $this->date = $date;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getDate(): \DateTime
    {
        return clone $this->date;
    }
}
