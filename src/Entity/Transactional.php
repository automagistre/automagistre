<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface Transactional
{
    public function getTransactionClass(): string;
}
