<?php

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Enum\TireFittingCategory;
use ArrayIterator;
use Doctrine\ORM\Mapping as ORM;
use IteratorAggregate;
use JsonSerializable;
use libphonenumber\PhoneNumber;
use Money\Currency;
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
     * @ORM\Column(type="vehicle_id", nullable=true)
     */
    public ?VehicleId $modelId;

    /**
     * @ORM\Column(type="tire_fitting_category")
     */
    public TireFittingCategory $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public ?int $diameter;

    /**
     * @ORM\Column(type="money")
     */
    public Money $total;

    /**
     * @ORM\Column(type="appeal_tire_fitting_work")
     */
    public TireWorkCollection $works;

    public function __construct(
        AppealId $id,
        string $name,
        PhoneNumber $phone,
        ?VehicleId $modelId,
        TireFittingCategory $category,
        ?int $diameter,
        Money $total,
        array $works
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->modelId = $modelId;
        $this->category = $category;
        $this->diameter = $diameter;
        $this->total = $total;
        $this->works = new TireWorkCollection($works);

        $this->record(new AppealCreated($this->id));
    }
}

/**
 * @template-implements IteratorAggregate<int, TireFittingWork>
 */
final class TireWorkCollection implements IteratorAggregate, JsonSerializable
{
    /**
     * @var TireFittingWork[]
     */
    private array $works = [];

    public function __construct(array $works)
    {
        foreach ($works as $work) {
            $this->works[] = new TireFittingWork($work);
        }
    }

    /**
     * @return ArrayIterator<int, TireFittingWork>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->works);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return $this->works;
    }
}

final class TireFittingWork implements JsonSerializable
{
    public string $name;

    public Money $price;

    public function __construct(array $work)
    {
        $this->name = $work['name'];
        $this->price = $work['price'] instanceof Money
            ? $work['price']
            : new Money($work['price']['amount'], new Currency($work['price']['currency']));
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
        ];
    }
}
