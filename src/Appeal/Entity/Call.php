<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_call")
 */
class Call implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="appeal_id")
     */
    public AppealId $id;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

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
