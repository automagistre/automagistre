<?php

declare(strict_types=1);

namespace App\Shared\Mercure;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NullPublisher implements PublisherInterface
{
    public function __invoke(Update $update): string
    {
        return '';
    }
}
