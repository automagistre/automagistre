<?php

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MC\Entity\McEquipmentId;
use App\MC\Entity\McWorkId;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Part\Entity\PartId;
use function array_filter;
use ArrayIterator;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use IteratorAggregate;
use JsonSerializable;
use libphonenumber\PhoneNumber;
use LogicException;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_calculator")
 */
class Calculator implements ContainsRecordedMessages
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
     * @ORM\Column(nullable=true)
     */
    public ?string $note;

    /**
     * @ORM\Column(type="phone_number")
     */
    public PhoneNumber $phone;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    public ?DateTimeImmutable $date;

    /**
     * @ORM\Column(type="mc_equipment_id")
     */
    public McEquipmentId $equipmentId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $mileage;

    /**
     * @ORM\Column(type="money")
     */
    public Money $total;

    /**
     * @ORM\Column(type="appeal_calculator_work")
     */
    public CalculatorWorkCollection $works;

    public function __construct(
        AppealId $id,
        string $name,
        ?string $note,
        PhoneNumber $phone,
        ?DateTimeImmutable $date,
        McEquipmentId $equipmentId,
        int $mileage,
        Money $total,
        array $works
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->note = $note;
        $this->phone = $phone;
        $this->date = $date;
        $this->equipmentId = $equipmentId;
        $this->mileage = $mileage;
        $this->total = $total;
        $this->works = new CalculatorWorkCollection($works);

        $this->record(new AppealCreated($this->id));
    }
}

/**
 * @template-implements IteratorAggregate<int, CalculatorWork>
 */
final class CalculatorWorkCollection implements IteratorAggregate, JsonSerializable
{
    public Money $total;

    public Money $recTotal;

    /**
     * @var CalculatorWork[]
     */
    private array $works = [];

    public function __construct(array $works)
    {
        $prices = [];
        $recPrices = [];
        foreach ($works as $work) {
            $this->works[] = $work = new CalculatorWork($work);

            if ($work->isSelected) {
                if ('work' === $work->type) {
                    $prices[] = $work->getTotal();
                } elseif ('recommendation' === $work->type) {
                    $recPrices[] = $work->getTotal();
                } else {
                    throw new LogicException('Unexpected type: '.$work->type);
                }
            }
        }
        $prices = array_filter($prices);
        $recPrices = array_filter($recPrices);

        $zero = Money::RUB(0);

        $this->total = [] === $prices ? $zero : Money::sum(...$prices);
        $this->recTotal = [] === $recPrices ? $zero : Money::sum(...$recPrices);
    }

    /**
     * @return ArrayIterator<int, CalculatorWork>
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

class CalculatorWork implements JsonSerializable
{
    public McWorkId $id;

    public string $name;

    public Money $price;

    public string $type;

    public bool $isSelected;

    /**
     * @var CalculatorPart[]
     */
    public array $parts = [];

    private ?Money $total;

    public function __construct(array $work)
    {
        $this->id = McWorkId::fromAny($work['id']);
        $this->name = $work['name'];
        $this->price = $work['price'] instanceof Money
            ? $work['price']
            : new Money($work['price']['amount'], new Currency($work['price']['currency']));
        $this->type = $work['type'];
        $this->isSelected = $work['isSelected'];

        $prices = [$this->price];
        foreach ($work['parts'] as $part) {
            $this->parts[] = $part = new CalculatorPart($part);
            $prices[] = $part->getTotal();
        }
        $this->total = [] === $prices ? null : Money::sum(...$prices);
    }

    public function getTotal(): ?Money
    {
        return $this->total;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'type' => $this->type,
            'isSelected' => $this->isSelected,
            'parts' => $this->parts,
        ];
    }
}

class CalculatorPart implements JsonSerializable
{
    public PartId $id;

    public string $name;

    public Money $price;

    public int $count;

    public bool $isSelected;

    private Money $total;

    public function __construct(array $part)
    {
        $this->id = PartId::fromAny($part['id']);
        $this->name = $part['name'];
        $this->price = $part['price'] instanceof Money
            ? $part['price']
            : new Money($part['price']['amount'], new Currency($part['price']['currency']));
        $this->count = $part['count'];
        $this->isSelected = $part['isSelected'];
        $this->total = $this->price->multiply($this->count / 100);
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'count' => $this->count,
            'isSelected' => $this->isSelected,
        ];
    }
}
