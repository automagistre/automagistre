<?php

declare(strict_types=1);

namespace App\MC\Documents;

use App\Part\Documents\Part;
use App\Shared\Money\Documents\Money;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Work
{
    /**
     * @ODM\Field()
     */
    public string $name;

    /**
     * @ODM\Field(nullable=true)
     */
    public ?string $description = null;

    /**
     * @ODM\Field(type="int")
     */
    public int $period;

    /**
     * @ODM\Field(type="bool")
     */
    public bool $recommended = false;

    /**
     * @ODM\EmbedOne(targetDocument=Money::class)
     */
    public Money $price;

    /**
     * @var Part[]
     *
     * @ODM\EmbedMany(targetDocument=Part::class)
     */
    public $parts;

    /**
     * @ODM\Field(type="int")
     */
    public int $position;

    public function __construct(
        string $name,
        ?string $description,
        int $period,
        bool $recommended,
        Money $price,
        array $parts,
        int $position
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->period = $period;
        $this->recommended = $recommended;
        $this->price = $price;
        $this->parts = $parts;
        $this->position = $position;
    }
}
