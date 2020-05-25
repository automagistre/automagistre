<?php

declare(strict_types=1);

namespace App\MC\Documents;

use App\Part\Documents\Part;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class McPart
{
    /**
     * @ODM\EmbedOne(targetDocument=Part::class)
     */
    public Part $part;

    /**
     * @ODM\Field(type="int")
     */
    public int $quantity;

    /**
     * @ODM\Field(type="bool")
     */
    public bool $recommended;

    public function __construct(Part $part, int $quantity, bool $recommended)
    {
        $this->part = $part;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }
}
