<?php

declare(strict_types=1);

namespace App\Payment\Manager;

use App\Balance\Entity\BalanceView;
use App\Doctrine\Registry;
use LogicException;
use Money\Money;
use Premier\Identifier\Identifier;
use function method_exists;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentManager
{
    public function __construct(private Registry $registry)
    {
    }

    public function balance(object $transactional): Money
    {
        if (method_exists($transactional, 'toId')) {
            $id = $transactional->toId();
        } elseif ($transactional instanceof Identifier) {
            $id = $transactional;
        } else {
            throw new LogicException('Unsupported transactional');
        }

        return $this->registry->getBy(BalanceView::class, ['id' => $id])->money;
    }
}
