<?php

declare(strict_types=1);

namespace App\Healthcheck\Controller;

use Symfony\Component\HttpFoundation\Response;

final class HealthcheckAction
{
    public function __invoke(): Response
    {
        return new Response('OK');
    }
}
