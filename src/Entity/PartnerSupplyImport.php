<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PartnerSupplyImport
{
    use Identity;

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

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(string $externalId, \DateTime $date)
    {
        $this->createdAt = new \DateTime();

        $this->externalId = $externalId;
        $this->date = $date;
    }

    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
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
