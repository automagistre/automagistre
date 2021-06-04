<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210604142202 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'Create supply_view';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
    }
}
