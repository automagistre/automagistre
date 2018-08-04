<?php

declare(strict_types=1);

namespace App\Router;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface ListeningRouterEvents
{
    public const PRE_GENERATE = 'listening_router.pre_generate';
}
