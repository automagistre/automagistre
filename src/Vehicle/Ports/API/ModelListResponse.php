<?php

declare(strict_types=1);

namespace App\Vehicle\Ports\API;

use Pagerfanta\Pagerfanta;

final class ModelListResponse
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
