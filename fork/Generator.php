<?php

declare(strict_types=1);

namespace Doctrine\Migrations\Generator;

use Symfony\Component\Filesystem\Filesystem;
use DateTime;
use function file_put_contents;

class Generator
{
    public function generateMigration(string $fqcn, ?string $up = null, ?string $down = null): string
    {
        $dir = __DIR__.'/../migrations/default/'.(new DateTime())->getTimestamp().'000_doctrine/';

        (new Filesystem())->mkdir($dir);

        file_put_contents($dir.'up.sql', $up ?? '');
        file_put_contents($dir.'down.sql', $down ?? '');

        return $dir;
    }
}
