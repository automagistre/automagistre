<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Keycloak\Entity\UserId;
use DateTimeImmutable;
use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_call")
 */
class Call extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public AppealId $id;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(AppealId $id, PhoneNumber $phone)
    {
        $this->id = $id;
        $this->phone = $phone;

        $this->record(new AppealCreated($this->id));
    }

    public static function create(PhoneNumber $phone): self
    {
        return new self(
            AppealId::generate(),
            $phone,
        );
    }
}
