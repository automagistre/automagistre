<?php

declare(strict_types=1);

namespace App\Storage\Form;

use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseView;
use Symfony\Component\Validator\Constraints as Assert;

final class WarehouseDto
{
    public WarehouseId $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $code;

    /**
     * @var null|WarehouseId
     */
    public $parentId;

    public function __construct(WarehouseId $id, string $name, string $code, ?WarehouseId $parentId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->parentId = $parentId;
    }

    public static function from(WarehouseView $view): self
    {
        return new self($view->id, $view->name, $view->code, $view->parent?->id);
    }
}
