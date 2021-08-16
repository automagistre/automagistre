<?php

declare(strict_types=1);

namespace App\Keycloak\Event;

use App\MessageBus\Async;
use App\Tenant\Enum\Tenant;

final class UserLoggedIn implements Async
{
    public function __construct(
        public string $username,
        public string $password,
        public Tenant $tenant,
    ) {
    }
}
