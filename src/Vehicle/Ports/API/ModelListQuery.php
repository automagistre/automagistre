<?php

declare(strict_types=1);

namespace App\Vehicle\Ports\API;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class ModelListQuery
{
    /**
     * @var Filtering[]
     *
     * @Assert\Valid()
     */
    public array $filtering = [];

    /**
     * @var Ordering[]
     *
     * @Assert\Valid()
     */
    public array $ordering = [];

    /**
     * @var Paging
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    public Paging $paging;

    public function __construct()
    {
        $this->paging = new Paging();
    }
}
