<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Part\Event\PartPriceChanged;
use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;

/**
 * @ORM\Entity
 * @ORM\Table(name="part_price", indexes={@ORM\Index(columns={"part_id"})})
 */
class Price extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\Column
     */
    private PartId $partId;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $price;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $since;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(PartId $partId, Money $price, DateTimeImmutable $since = null)
    {
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->price = $price;
        $this->since = $since ?? new DateTimeImmutable();

        $this->record(new PartPriceChanged($this->partId));
    }
}
