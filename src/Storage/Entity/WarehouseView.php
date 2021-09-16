<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use function array_map;
use function array_reverse;
use function implode;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="warehouse_view")
 *
 * @psalm-suppress MissingConstructor
 */
class WarehouseView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public WarehouseId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column
     */
    public string $code;

    /**
     * @ORM\Column(type="integer")
     */
    public int $depth;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseView::class, fetch="EAGER")
     */
    public ?WarehouseView $parent = null;

    private ?array $path = null;

    public function toId(): WarehouseId
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return implode(' / ', array_map(static fn (self $view) => $view->name, $this->path()));
    }

    public function path(): array
    {
        return $this->path ?? (static function (self $view): array {
            $path = [$view];

            $parent = $view->parent;
            while (null !== $parent) {
                $path[] = $parent;

                $parent = $parent->parent;
            }

            return array_reverse($path);
        })($this);
    }
}
