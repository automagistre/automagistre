<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Customer\Entity\OperandId;
use App\Manufacturer\Entity\ManufacturerView;
use App\Note\Entity\Notes;
use App\Note\Enum\NoteType;
use App\Part\Enum\Unit;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function array_map;
use function sprintf;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_view")
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
class PartView implements Notes
{
    /**
     * Наценка на запчасти.
     */
    private const MARKUP = 1.15;

    /**
     * @ORM\Id
     * @ORM\Column(type="part_id")
     */
    public PartId $id;

    /**
     * @ORM\Embedded(class=ManufacturerView::class)
     */
    public ManufacturerView $manufacturer;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="part_number")
     */
    public PartNumber $number;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $isUniversal;

    /**
     * @ORM\Column(type="unit_enum")
     */
    public Unit $unit;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    public int $ordered;

    /**
     * @ORM\Column(type="integer")
     */
    public int $reserved;

    /**
     * @ORM\Column(type="money")
     */
    public Money $price;

    /**
     * @ORM\Column(type="money")
     */
    public Money $discount;

    /**
     * @ORM\Column(type="money")
     */
    public Money $income;

    /**
     * @var array<int, PartId>
     *
     * @ORM\Column(type="part_ids")
     */
    public array $analogs;

    /**
     * @ORM\Column(type="integer")
     */
    public int $orderFromQuantity;

    /**
     * @ORM\Column(type="integer")
     */
    public int $orderUpToQuantity;

    /**
     * @ORM\Column(type="integer")
     */
    public int $suppliesQuantity;

    /**
     * @ORM\Column
     */
    private string $search;

    /**
     * @ORM\Column
     */
    private string $cases;

    /**
     * @ORM\Column(type="json")
     */
    private array $supplies;

    /**
     * @var array<int, array{type: int, text: string}>
     *
     * @ORM\Column(type="json")
     */
    private array $notes;

    public function toId(): PartId
    {
        return $this->id;
    }

    public function display(): string
    {
        return sprintf(
            '%s - %s (%s)',
            $this->manufacturer->name,
            $this->name,
            (string) $this->number,
        );
    }

    public function displayWithStock(): string
    {
        return sprintf(
            '%s - %s (%s) (Склад: %s %s., Резерв: %s %s.)',
            $this->manufacturer->name,
            $this->name,
            (string) $this->number,
            $this->quantity / 100,
            $this->unit->toShortLabel(),
            $this->ordered / 100,
            $this->unit->toShortLabel(),
        );
    }

    public function sellPrice(): Money
    {
        return $this->price->subtract($this->discount);
    }

    public function suggestPrice(): Money
    {
        $suggestPrice = $this->price;
        $incomePriceWithMarkup = $this->income->multiply(self::MARKUP);

        if ($incomePriceWithMarkup->greaterThan($suggestPrice)) {
            $suggestPrice = $incomePriceWithMarkup;
        }

        return $suggestPrice;
    }

    /**
     * @return SupplyView[]
     */
    public function supplies(): array
    {
        $supplies = [];

        foreach ($this->supplies as $supply) {
            $supplies[] = new SupplyView(
                $this->id,
                OperandId::from($supply['supplier_id']),
                $supply['quantity'],
                new DateTimeImmutable($supply['updatedAt']),
            );
        }

        return $supplies;
    }

    public function hasExpiredSupplies(): bool
    {
        foreach ($this->supplies as $supply) {
            if (new DateTimeImmutable($supply['updatedAt']) < new DateTimeImmutable('-1 week')) {
                return true;
            }
        }

        return false;
    }

    public function notes(): iterable
    {
        return array_map(
            static fn (array $note) => [
                'type' => NoteType::create($note['type']),
                'text' => $note['text'],
            ],
            $this->notes,
        );
    }

    public function hasKeepingStock(): bool
    {
        return 0 !== $this->orderFromQuantity || 0 !== $this->orderUpToQuantity;
    }
}
