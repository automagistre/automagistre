<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Review\Enum\ReviewRating;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function is_int;
use function json_decode;
use function json_encode;
use function strpos;
use function substr;
use function trim;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

final class Version20210102155106 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS review_view');

        // Text
        $this->addSql('ALTER TABLE review RENAME content TO text');

        // Rating
        $this->addSql('ALTER TABLE review ADD rating SMALLINT DEFAULT NULL');
        $this->addSql('UPDATE review SET rating = 0 WHERE rating IS NULL');
        $this->addSql('ALTER TABLE review ALTER rating SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN review.rating IS \'(DC2Type:review_star_rating)\'');

        // SourceId
        $this->addSql('ALTER TABLE review ADD source_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE review SET source_id = id WHERE source_id IS NULL');
        $this->addSql('ALTER TABLE review ALTER source_id SET NOT NULL');

        // Raw
        $this->addSql('ALTER TABLE review ADD raw JSON DEFAULT NULL');
        $this->addSql(
            <<<'SQL'
            UPDATE review SET raw = JSON_BUILD_OBJECT(
                'manufacturer', manufacturer,
                'model', model,
                'source', source
                )
                WHERE raw IS NULL
            SQL
        );
        $this->addSql('ALTER TABLE review ALTER raw SET NOT NULL');

        // Drop
        $this->addSql('ALTER TABLE review DROP manufacturer');
        $this->addSql('ALTER TABLE review DROP model');

        // Source
        $this->addSql('UPDATE review SET source = 1');
        $this->addSql('ALTER TABLE review ALTER source TYPE SMALLINT USING (source::SMALLINT)');
        $this->addSql('ALTER TABLE review ALTER source DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN review.source IS \'(DC2Type:review_source)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_794381C65F8A7F73953C1C61 ON review (source, source_id)');

        // Yandex
        foreach ($this->connection->fetchAllAssociative('SELECT * FROM yandex_map_review') as $item) {
            $payload = json_decode($item['payload'], true, 512, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

            $this->addSql(
                '
                INSERT INTO review (id, source, source_id, rating, author, text, publish_at, raw)
                VALUES (:id, :source, :sourceId, :rating, :author, :text, :publishAt::TIMESTAMP, :raw)
                ',
                [
                    'id' => $item['id'],
                    'source' => 2,
                    'sourceId' => $item['review_id'],
                    'rating' => $payload['rating'],
                    'author' => $payload['author']['name'] ?? '',
                    'text' => $payload['text'],
                    'publishAt' => $payload['updatedTime'],
                    'raw' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                ]
            );
        }
        $this->addSql('DROP TABLE yandex_map_review');

        // Google
        foreach ($this->connection->fetchAllAssociative('SELECT * FROM google_review') as $item) {
            $payload = json_decode($item['payload'], true, 512, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

            $comment = $payload['comment'] ?? '';
            $transPos = strpos($comment, '(Translated by Google)');

            if (is_int($transPos)) {
                $comment = trim(substr($comment, 0, $transPos));
            }

            $this->addSql(
                '
                INSERT INTO review (id, source, source_id, rating, author, text, publish_at, raw)
                VALUES (:id, :source, :sourceId, :rating, :author, :text, :publishAt::TIMESTAMP, :raw)
                ',
                [
                    'id' => $item['id'],
                    'source' => 3,
                    'sourceId' => $item['review_id'],
                    'rating' => ReviewRating::fromGoogleValue($payload['starRating'])->toId(),
                    'author' => $payload['reviewer']['displayName'],
                    'text' => $comment,
                    'publishAt' => $payload['createTime'] ?? $payload['updatedTime'],
                    'raw' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                ]
            );
        }
        $this->addSql('ALTER TABLE google_review_token DROP expire_id');
        $this->addSql('DROP TABLE google_review');
        $this->addSql('DROP TABLE google_review_token_expire');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review ADD model VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE review ADD content TEXT NOT NULL');
        $this->addSql('ALTER TABLE review DROP text');
        $this->addSql('ALTER TABLE review DROP rating');
        $this->addSql('ALTER TABLE review DROP raw');
        $this->addSql('ALTER TABLE review ALTER source TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE review ALTER source DROP DEFAULT');
        $this->addSql('ALTER TABLE review RENAME COLUMN source_id TO manufacturer');
        $this->addSql('COMMENT ON COLUMN review.source IS NULL');
    }
}
