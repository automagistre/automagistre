<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Manufacturer\Entity\ManufacturerView;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function sprintf;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_view")
 */
class PartView
{
    /**
     * Наценка на запчасти.
     */
    private const MARKUP = 1.15;

    /**
     * @ORM\Id()
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
     * @ORM\Column()
     */
    public string $number;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $isUniversal;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

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
     * @ORM\Column(type="json")
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
     * @ORM\Column()
     */
    private string $search;

    /**
     * @ORM\Column()
     */
    private string $cases;

    public function __construct(
        PartId $id,
        ManufacturerView $manufacturer,
        string $name,
        string $number,
        bool $isUniversal,
        int $quantity,
        Money $price,
        Money $discount,
        Money $income,
        array $analogs,
        int $orderFromQuantity,
        int $orderUpToQuantity,
        string $search,
        string $cases
    ) {
        $this->id = $id;
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->number = $number;
        $this->isUniversal = $isUniversal;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->discount = $discount;
        $this->income = $income;
        $this->analogs = $analogs;
        $this->orderFromQuantity = $orderFromQuantity;
        $this->orderUpToQuantity = $orderUpToQuantity;
        $this->search = $search;
        $this->cases = $cases;
    }

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
            $this->number,
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

    public static function sql(): string
    {
        return '
            CREATE VIEW part_view AS
            SELECT part.id                                                                                          AS id,
                   part.name                                                                                        AS name,
                   part.number                                                                                      AS number,
                   part.universal                                                                                   AS is_universal,
                   COALESCE(stock.quantity, 0)                                                                      AS quantity,
                   COALESCE(crosses.parts, \'[]\'::JSON)                                                              AS analogs,
                   m.name                                                                                           AS manufacturer_name,
                   m.id                                                                                             AS manufacturer_id,
                   pc.cases                                                                                         AS cases,
                   UPPER(concat_ws(\' \', part.name, m.name, m.localized_name, pc.cases))                             AS search,
                   COALESCE(price.price_currency_code, \'RUB\') || \' \' || COALESCE(price.price_amount, 0)             AS price,
                   COALESCE(discount.discount_currency_code, \'RUB\') || \' \' || COALESCE(discount.discount_amount, 0) AS discount,
                   COALESCE(income.price_currency_code, \'RUB\') || \' \' || COALESCE(income.price_amount, 0)           AS income,
                   COALESCE(part_required.order_from_quantity, 0)                                                       AS order_from_quantity,
                   COALESCE(part_required.order_up_to_quantity, 0)                                                      AS order_up_to_quantity
            FROM part
                     JOIN manufacturer m ON part.manufacturer_id = m.id
                     LEFT JOIN (SELECT part_case.part_id, array_to_string(array_agg(vm.case_name), \' \') AS cases
                                FROM part_case
                                         LEFT JOIN vehicle_model vm ON vm.id = part_case.vehicle_id
                                GROUP BY part_case.part_id) pc ON pc.part_id = part.id
                     LEFT JOIN (SELECT row_number() OVER (PARTITION BY pra.part_id ORDER BY pra.id DESC) AS rownum,
                                       pra.*
                                FROM part_required_availability pra) part_required ON part_required.part_id = part.id AND part_required.rownum = 1
                     LEFT JOIN (SELECT row_number() OVER (PARTITION BY pp.part_id ORDER BY pp.id DESC) AS rownum,
                                       pp.*
                                FROM part_price pp) price ON price.part_id = part.id AND price.rownum = 1
                     LEFT JOIN (SELECT row_number() OVER (PARTITION BY pd.part_id ORDER BY pd.id DESC) AS rownum,
                                       pd.*
                                FROM part_discount pd) discount ON discount.part_id = part.id AND discount.rownum = 1
                     LEFT JOIN (SELECT row_number() OVER (PARTITION BY income_part.part_id ORDER BY income_part.id DESC) AS rownum,
                                       income_part.*
                                FROM income_part) income ON income.part_id = part.id AND income.rownum = 1
                     LEFT JOIN (SELECT motion.part_id, SUM(motion.quantity) AS quantity FROM motion GROUP BY motion.part_id) stock
                               ON stock.part_id = part.id
                     LEFT JOIN (SELECT pcp.part_id, json_agg(pcp2.part_id) AS parts
                                FROM part_cross_part pcp
                                         JOIN part_cross pc ON pcp.part_cross_id = pc.id
                                         LEFT JOIN part_cross_part pcp2 ON pcp2.part_cross_id = pc.id
                                GROUP BY pcp.part_id) crosses ON crosses.part_id = part.id
        ';
    }
}
