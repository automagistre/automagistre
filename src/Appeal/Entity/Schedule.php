<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_schedule")
 */
class Schedule implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="appeal_id")
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

    /**
     * @ORM\Column(type="date_immutable")
     */
    public DateTimeImmutable $date;

    public function __construct(AppealId $id, string $name, PhoneNumber $phone, DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->date = $date;

        $this->record(new AppealCreated($this->id));
    }

    public static function create(string $name, PhoneNumber $phone, DateTimeImmutable $date): self
    {
        return new self(
            AppealId::generate(),
            $name,
            $phone,
            $date,
        );
    }
}
