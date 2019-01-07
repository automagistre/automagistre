<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Wallet;
use Money\Currency;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletController extends AbstractController
{
    protected function createNewEntity(): Wallet
    {
        $entity = new Wallet();
        $entity->currency = new Currency('RUB');

        return $entity;
    }
}
