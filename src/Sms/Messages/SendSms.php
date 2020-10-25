<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use App\Customer\Entity\OperandId;
use App\Sms\Enum\Feature;
use function array_key_exists;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;

/**
 * @psalm-immutable
 */
final class SendSms
{
    /**
     * @var OperandId|PhoneNumber
     */
    public $recipient;

    public string $message;

    public ?DateTimeImmutable $dateSend;

    private array $features = [];

    /**
     * @param OperandId|PhoneNumber $recipient
     * @param Feature[]             $features
     */
    public function __construct($recipient, string $message, array $features = [], DateTimeImmutable $dateSend = null)
    {
        $this->recipient = clone $recipient;
        $this->message = $message;

        foreach ($features as $feature) {
            $this->features[$feature->toId()] = true;
        }
        $this->dateSend = $dateSend;
    }

    public function hasFeature(Feature $feature): bool
    {
        return array_key_exists($feature->toId(), $this->features);
    }
}
