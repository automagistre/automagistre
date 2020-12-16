<?php

declare(strict_types=1);

namespace App\Review\Document;

use App\Review\Entity\ReviewId;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 * @psalm-suppress MissingConstructor
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="review_view")
 *
 * @ODM\Document(collection="review")
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\Column(type="review_id")
     *
     * @ODM\Id(strategy="NONE", type="review_id")
     */
    public ReviewId $id;

    /**
     * @ORM\Column
     *
     * @ODM\Field
     */
    public string $author;

    /**
     * @ORM\Column(type="text")
     *
     * @ODM\Field
     */
    public string $content;

    /**
     * @ORM\Column
     *
     * @ODM\Field
     */
    public string $source;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @ODM\Field(type="date")
     */
    public DateTimeImmutable $publishAt;

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
