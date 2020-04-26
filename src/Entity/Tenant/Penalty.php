<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Penalty
{
    use Identity;

    /**
     * @var OperandTransaction
     *
     * @ORM\ManyToOne(targetEntity=OperandTransaction::class)
     */
    private $transaction;

    /**
     * @var string|null
     *
     * @ORM\Column
     */
    private $description;

    public function __construct(OperandTransaction $transaction, string $description)
    {
        $this->transaction = $transaction;
        $this->description = $description;
    }

    public function getTransaction(): OperandTransaction
    {
        return $this->transaction;
    }
}
