<?php

declare(strict_types=1);

namespace App\Sms\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Sms\Messages\SendRequested;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity
 *
 * @psalm-immutable
 */
class Sms implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="sms_id")
     */
    public SmsId $id;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phoneNumber;

    /**
     * @ORM\Column
     */
    public string $message;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public ?DateTimeImmutable $dateSend;

    public function __construct(PhoneNumber $phoneNumber, string $message, DateTimeImmutable $dateSend = null)
    {
        $this->id = SmsId::generate();
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->dateSend = $dateSend;

        $this->record(new SendRequested($this->id));
    }
}
