<?php

declare(strict_types=1);

namespace App\Manufacturer\Ports\API;

use Pagerfanta\Pagerfanta;

final class ManufacturerListResponse
{
    public Pagerfanta $pager;

    public function __construct(Pagerfanta $pager)
    {
        $this->pager = $pager;
    }

    public function toArray(): array
    {
        return [
            'list' => $this->pager->getCurrentPageResults(),
            'count' => $this->pager->count(),
            'paging' => [
                'page' => $this->pager->getCurrentPage(),
                'size' => $this->pager->getMaxPerPage(),
            ],
        ];
    }
}
