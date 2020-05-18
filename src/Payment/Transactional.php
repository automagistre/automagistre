<?php

declare(strict_types=1);

namespace App\Payment;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface Transactional
{
    public function __toString(): string;

    /**
     * @psalm-return class-string
     */
    public function getTransactionClass(): string;
}
