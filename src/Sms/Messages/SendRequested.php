<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use App\Sms\Entity\SmsId;

/**
 * @psalm-immutable
 */
final class SendRequested
{
    public SmsId $smsId;

    public function __construct(SmsId $smsId)
    {
        $this->smsId = $smsId;
    }
}
