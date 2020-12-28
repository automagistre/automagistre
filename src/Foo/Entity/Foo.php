<?php

declare(strict_types=1);

namespace App\Foo\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Foo
{
    /**
     * @var UuidInterface|null
     *
     * @ORM\Id
     * @ORM\Column
     */
    public $id;

    final public function __construct()
    {
    }
}
