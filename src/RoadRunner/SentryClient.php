<?php

declare(strict_types=1);

namespace App\RoadRunner;

use Sentry\SentryBundle\SentrySymfonyClient;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SentryClient extends SentrySymfonyClient
{
    protected static function is_http_request(): bool
    {
        return \defined('RR_WORKER') || parent::is_http_request();
    }
}
