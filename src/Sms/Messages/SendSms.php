<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use App\Customer\Entity\OperandId;
use App\Sms\Enum\Feature;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;
use function array_key_exists;

/**
 * @psalm-immutable
 */
final class SendSms
{
    /**
     * @var OperandId|PhoneNumber
     */
    public $recipient;

    private array $features = [];

    /**
     * @param OperandId|PhoneNumber $recipient
     * @param Feature[]             $features
     */
    public function __construct(
        $recipient,
        public string $message,
        array $features = [],
        public ?DateTimeImmutable $dateSend = null,
    ) {
        $this->recipient = clone $recipient;

        foreach ($features as $feature) {
            $this->features[$feature->toId()] = true;
        }
    }

    public function hasFeature(Feature $feature): bool
    {
        return array_key_exists($feature->toId(), $this->features);
    }
}
