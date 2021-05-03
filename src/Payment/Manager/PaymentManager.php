<?php

declare(strict_types=1);

namespace App\Payment\Manager;

use App\Balance\Entity\BalanceView;
use App\Customer\Entity\Operand;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Wallet\Entity\Wallet;
use LogicException;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentManager
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function balance(object $transactional): Money
    {
        if ($transactional instanceof Operand) {
            $id = $transactional->toId();
        } elseif ($transactional instanceof Wallet) {
            $id = $transactional->toId();
        } elseif ($transactional instanceof Identifier) {
            $id = $transactional;
        } else {
            throw new LogicException('Unsupported transactional');
        }

        return $this->registry->getBy(BalanceView::class, ['id' => $id])->money;
    }
}
