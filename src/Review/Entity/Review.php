<?php

declare(strict_types=1);

namespace App\Review\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use App\Review\Event\ReviewReceived;
use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"source", "source_id", "tenant_id"})
 *     }
 * )
 */
class Review extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public ReviewId $id;

    /**
     * @ORM\Column
     */
    public string $sourceId;

    /**
     * @ORM\Column(type="review_source")
     */
    public ReviewSource $source;

    /**
     * @ORM\Column
     */
    public string $author;

    /**
     * @ORM\Column(type="text")
     */
    public string $text;

    /**
     * @ORM\Column(type="review_star_rating")
     */
    public ReviewRating $rating;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $publishAt;

    /**
     * @ORM\Column(type="json")
     */
    public array $raw;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(
        ReviewId $id,
        string $sourceId,
        ReviewSource $source,
        string $author,
        string $text,
        ReviewRating $rating,
        DateTimeImmutable $publishAt,
        array $raw,
    ) {
        $this->id = $id;
        $this->sourceId = $sourceId;
        $this->source = $source;
        $this->author = $author;
        $this->text = $text;
        $this->rating = $rating;
        $this->publishAt = $publishAt;
        $this->raw = $raw;

        $this->record(new ReviewReceived($this->id));
    }

    public function toId(): ReviewId
    {
        return $this->id;
    }
}
