<?php

declare(strict_types=1);

namespace App\Sms\Event;

use App\Sms\Entity\SmsId;

/**
 * @psalm-immutable
 */
final class SmsSendRequested
{
    public SmsId $smsId;

    public function __construct(SmsId $smsId)
    {
        $this->smsId = $smsId;
    }
}
