<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use App\Sms\Entity\SmsId;

/**
 * @psalm-immutable
 */
final class SendRequested
{
    public function __construct(public SmsId $smsId)
    {
    }
}
