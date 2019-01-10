<?php

declare(strict_types=1);

namespace App;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Roles
{
    public const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const ADMIN = 'ROLE_ADMIN';

    /**
     * Доступ в админку.
     */
    public const EMPLOYEE = 'ROLE_EMPLOYEE';

    public const MAINTENANCE_CONFIGURATOR = 'ROLE_MAINTENANCE_CONFIGURATOR';

    public const CUSTOMER_FEEDBACK = 'ROLE_CUSTOMER_FEEDBACK';

    public const ROLE_USER_MANAGER = 'ROLE_USER_MANAGER';
}
