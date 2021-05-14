<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Migrations\Event\MigrationsEventArgs;
use Doctrine\Migrations\Events;
use Symfony\Component\Finder\Finder;
use function sprintf;
use function Symfony\Component\String\u;
use function Safe\file_get_contents;

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

    /**
     * Кеш чтобы не бегать дважды по файлам.
     */
    private array $views = [];

    /**
     * {@inheritdoc}
     */
    public function onMigrationsMigrating(MigrationsEventArgs $event): void
    {
        $conn = $event->getConnection();

        foreach ((new Finder())->in($this->path)->files() as $file) {
            $name = u($file->getBasename())->replace('.sql', '_view')->snake()->toString();

            $conn->executeQuery(sprintf('DROP VIEW IF EXISTS %s', $name));

            $this->views[$name] = $file->getRealPath();
        }
    }

    public function onMigrationsMigrated(MigrationsEventArgs $event): void
    {
        $conn = $event->getConnection();

        foreach ($this->views as $name => $file) {
            $conn->executeQuery(sprintf('CREATE VIEW %s AS %s', $name, file_get_contents($file)));
        }
    }
}
