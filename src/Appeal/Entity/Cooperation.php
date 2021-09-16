<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_cooperation")
 */
class Cooperation extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public AppealId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    public function __construct(AppealId $id, string $name, PhoneNumber $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;

        $this->record(new AppealCreated($this->id));
    }

    public static function create(string $name, PhoneNumber $phone): self
    {
        return new self(
            AppealId::generate(),
            $name,
            $phone,
        );
    }
}
