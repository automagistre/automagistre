<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Migrations\Event\MigrationsEventArgs;
use Doctrine\Migrations\Events;
use Symfony\Component\Finder\Finder;
use function Safe\file_get_contents;
use function sprintf;
use function Symfony\Component\String\u;

final class RecreateViewsOnMigration implements EventSubscriber
{
    public function __construct(private string $path)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onMigrationsMigrating,
            Events::onMigrationsMigrated,
        ];
    }

    public function onMigrationsMigrating(MigrationsEventArgs $event): void
    {
        $conn = $event->getConnection();

        $views = $conn->fetchFirstColumn("SELECT table_name FROM information_schema.views WHERE table_name ~ '_view$'");

        foreach ($views as $view) {
            $conn->executeQuery(sprintf('DROP VIEW %s', $view));
        }
    }

    public function onMigrationsMigrated(MigrationsEventArgs $event): void
    {
        $conn = $event->getConnection();

        foreach ((new Finder())->in($this->path)->files() as $file) {
            $name = u($file->getBasename())->replace('.sql', '_view')->snake()->toString();

            $conn->executeQuery(sprintf('CREATE VIEW %s AS %s', $name, file_get_contents($file->getPathname())));
        }
    }
}
