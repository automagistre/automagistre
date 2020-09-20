<?php

declare(strict_types=1);

namespace App\Sms\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Sms\Event\SmsSendRequested;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity
 */
class Sms implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id()
     * @ORM\Column(type="sms_id")
     */
    private SmsId $id;

    /**
     * @ORM\Column(type="phone_number")
     */
    private PhoneNumber $phoneNumber;

    /**
     * @ORM\Column()
     */
    private string $message;

    public function __construct(PhoneNumber $phoneNumber, string $message)
    {
        $this->id = SmsId::generate();
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;

        $this->record(new SmsSendRequested($this->id));
    }
}
