<?php

declare(strict_types=1);

namespace App\Entity;

use App\Uuid\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class PartnerSupplyImport
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column()
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
        $this->id = UuidGenerator::generate();
        $this->createdAt = new \DateTime();

        $this->externalId = $externalId;
        $this->date = $date;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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
