<?php

declare(strict_types=1);

namespace App\Part\Documents;

use App\Manufacturer\Documents\Manufacturer;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use App\Shared\Money\Documents\Money;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Part
{
    /**
     * @ODM\Field(type="part_id")
     */
    public PartId $id;

    /**
     * @ODM\EmbedOne(targetDocument=Manufacturer::class)
     */
    public Manufacturer $manufacturer;

    /**
     * @ODM\Field()
     */
    public string $name;

    /**
     * @ODM\Field()
     */
    public string $number;

    /**
     * @ODM\Field(type="bool")
     */
    public bool $universal;

    /**
     * @ODM\EmbedOne(targetDocument=Money::class)
     */
    public Money $price;

    /**
     * @ODM\EmbedOne(targetDocument=Money::class)
     */
    public Money $discount;

    public function __construct(
        PartId $id,
        Manufacturer $manufacturer,
        string $name,
        PartNumber $number,
        bool $universal,
        Money $price,
        Money $discount
    ) {
        $this->id = $id;
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->number = $number->number;
        $this->universal = $universal;
        $this->price = $price;
        $this->discount = $discount;
    }
}
