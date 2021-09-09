<?php

declare(strict_types=1);

namespace App\Keycloak\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class InvalidState extends AuthenticationException
{
}
