<?php

declare(strict_types=1);

namespace App\Wallet\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletId;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use function assert;
use Money\Currency;
use Money\Money;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletController extends AbstractController
{
    protected function createNewEntity(): stdClass
    {
        $dto = parent::createNewEntity();
        assert($dto instanceof stdClass);

        $dto->currency = new Currency('RUB');

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Wallet
    {
        $dto = $entity;
        assert($dto instanceof stdClass);

        $entity = new Wallet(
            WalletId::generate(),
            $dto->name,
            $dto->currency,
            $dto->useInIncome,
            $dto->useInOrder,
            $dto->showInLayout,
        );

        $initial = $dto->initial;
        if ($initial instanceof Money && $initial->isPositive()) {
            $em = $this->registry->manager();

            $em->persist(new WalletTransaction(
                WalletTransactionId::generate(),
                $entity->toId(), $initial,
                WalletTransactionSource::initial(),
                $this->getUser()->toId()->toUuid(),
                null
            ));
        }

        parent::persistEntity($entity);

        return $entity;
    }
}
