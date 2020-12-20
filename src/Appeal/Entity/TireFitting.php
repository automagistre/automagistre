<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Enum\BodyType;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_tire_fitting")
 */
class TireFitting implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="appeal_id")
     */
    public AppealId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    /**
     * @ORM\Column(type="vehicle_id")
     */
    public VehicleId $modelId;

    /**
     * @ORM\Column(type="carcase_enum", nullable=true)
     */
    public ?BodyType $bodyType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public ?int $diameter;

    /**
     * @ORM\Column(type="money")
     */
    public Money $total;

    /**
     * @ORM\Column(type="json")
     */
    public array $works;

    public function __construct(
        AppealId $id,
        string $name,
        PhoneNumber $phone,
        VehicleId $modelId,
        ?BodyType $bodyType,
        ?int $diameter,
        Money $total,
        array $works
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->modelId = $modelId;
        $this->bodyType = $bodyType;
        $this->diameter = $diameter;
        $this->total = $total;
        $this->works = $works;

        $this->record(new AppealCreated($this->id));
    }

    public static function create(
        string $name,
        PhoneNumber $phone,
        VehicleId $modelId,
        ?BodyType $bodyType,
        ?int $diameter,
        Money $total,
        array $works
    ): self {
        return new self(
            AppealId::generate(),
            $name,
            $phone,
            $modelId,
            $bodyType,
            $diameter,
            $total,
            $works,
        );
    }
}
