<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerTransactionSource;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

interface Transactional
{
    public function addTransaction(
        Money $money,
        CustomerTransactionSource $source,
        UuidInterface $sourceId,
        string $description = null
    ): void;
}
