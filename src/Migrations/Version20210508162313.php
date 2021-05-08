<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Costil;
use App\Storage\Enum\MotionType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function Safe\sprintf;

final class Version20210508162313 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            sprintf(
                'UPDATE motion SET source_type = %s, source_id = \'%s\'::uuid WHERE source_type = 0',
                MotionType::manual()->toId(),
                Costil::OLD_USER,
            ),
        );
    }
}
