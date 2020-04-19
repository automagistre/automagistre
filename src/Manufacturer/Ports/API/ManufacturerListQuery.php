<?php

declare(strict_types=1);

namespace App\Manufacturer\Ports\API;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class ManufacturerListQuery
{
    /**
     * @var ManufacturerListFiltering[]
     *
     * @Assert\Valid()
     */
    public array $filtering = [];

    /**
     * @var ManufacturerListOrdering[]
     *
     * @Assert\Valid()
     */
    public array $ordering = [];

    /**
     * @var Paging
     *
     * @Assert\NotBlank
     * @Assert\Valid
     */
    public $paging;

    public function __construct()
    {
        $this->paging = new Paging();
    }
}
