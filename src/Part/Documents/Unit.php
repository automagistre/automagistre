<?php

namespace App\Part\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Unit
{
    /**
     * @ODM\Field(type="integer")
     */
    public int $id;

    /**
     * @ODM\Field
     */
    public string $label;

    /**
     * @ODM\Field
     */
    public string $shortLabel;

    public function __construct(int $id, string $label, string $shortLabel)
    {
        $this->id = $id;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
    }

    public static function fromUnit(\App\Part\Enum\Unit $unit): self
    {
        return new self(
            $unit->toId(),
            $unit->toLabel(),
            $unit->toShortLabel(),
        );
    }
}
