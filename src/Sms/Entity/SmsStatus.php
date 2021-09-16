<?php

declare(strict_types=1);

namespace App\Sms\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class SmsStatus
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\Column
     */
    private SmsId $smsId;

    /**
     * @ORM\Column(type="json")
     */
    private array $payload;

    public function __construct(SmsId $smsId, array $payload)
    {
        $this->id = Uuid::uuid6();
        $this->smsId = $smsId;
        $this->payload = $payload;
    }
}
