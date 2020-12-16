<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Review\Document\Review;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20201216184849 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS review_view');

        $reviews = $this->connection->fetchAllAssociative('SELECT id, source FROM review_view ORDER BY publish_at');
        foreach ($reviews as $review) {
            $id = Uuid::uuid6()->toString();

            $table = ['club' => 'review', 'yandex' => 'yandex_map_review'][$review['source']];

            $this->addSql(sprintf('UPDATE %s SET id = :id WHERE id = :old', $table), [
                'id' => $id,
                'old' => $review['id'],
            ]);
        }

        $this->addSql(Review::sql());
    }
}
