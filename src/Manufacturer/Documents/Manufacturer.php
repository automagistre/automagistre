<?php

namespace App\Manufacturer\Documents;

use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Entity\ManufacturerView;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Manufacturer
{
    /**
     * @ODM\Field(type="manufacturer_id")
     */
    public ManufacturerId $id;

    /**
     * @ODM\Field
     */
    public string $name;

    /**
     * @ODM\Field(nullable=true)
     */
    private ?string $localizedName;

    public function __construct(ManufacturerId $id, string $name, ?string $localizedName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->localizedName = $localizedName;
    }

    public static function fromView(ManufacturerView $view): self
    {
        return new self(
            $view->id,
            $view->name,
            $view->localizedName,
        );
    }
}
