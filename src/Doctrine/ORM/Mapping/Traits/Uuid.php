<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait Uuid
{
    /**
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid_binary", unique=true)
     */
    private $uuid;

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    protected function generateUuid(): void
    {
        if ($this->uuid instanceof UuidInterface) {
            throw new LogicException('Uuid already defined.');
        }

        $this->uuid = \Ramsey\Uuid\Uuid::uuid4();
    }
}
