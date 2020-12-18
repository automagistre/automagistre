<?php

declare(strict_types=1);

namespace App\Review\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 * @psalm-suppress MissingConstructor
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="review_view")
 */
class ReviewView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="review_id")
     */
    public ReviewId $id;

    /**
     * @ORM\Column
     */
    public string $author;

    /**
     * @ORM\Column(type="text")
     */
    public string $content;

    /**
     * @ORM\Column
     */
    public string $source;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $publishAt;

    public function toId(): ReviewId
    {
        return $this->id;
    }

    public static function sql(): string
    {
        return <<<'SQL'
            CREATE VIEW review_view AS
            SELECT id,
                   author,
                   content,
                   publish_at,
                   'club' AS source
            FROM review
            UNION ALL
            SELECT id,
                   payload -> 'author' ->> 'name',
                   payload ->> 'text',
                   (payload ->> 'updatedTime')::DATE,
                   'yandex' AS source
            FROM yandex_map_review
            WHERE payload -> 'author' ->> 'name' IS NOT NULL
            SQL;
    }
}
