<?php

declare(strict_types=1);

namespace App\Router;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RoutePreGenerate extends GenericEvent
{
}
