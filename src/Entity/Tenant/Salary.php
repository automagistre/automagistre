<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
final class Salary
{
    use Identity;

    /**
     * @var OperandTransaction
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\OperandTransaction")
     */
    private $transaction;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $description;

    public function __construct(OperandTransaction $transaction, string $description = null)
    {
        $this->transaction = $transaction;
        $this->description = $description;
    }

    public function getTransaction(): OperandTransaction
    {
        return $this->transaction;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
