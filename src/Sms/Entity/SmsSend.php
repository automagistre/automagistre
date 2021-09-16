<?php

declare(strict_types=1);

namespace App\Sms\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class SmsSend extends TenantEntity
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
     * @ORM\Column(type="boolean")
     */
    private bool $success;

    /**
     * @ORM\Column(type="json")
     */
    private array $payload;

    public function __construct(SmsId $smsId, bool $success, array $payload)
    {
        $this->id = Uuid::uuid6();
        $this->smsId = $smsId;
        $this->success = $success;
        $this->payload = $payload;
    }
}
