<?php

declare(strict_types=1);

namespace App\Wallet\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Wallet\Entity\Wallet;
use function assert;
use Money\Currency;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletController extends AbstractController
{
    protected function createNewEntity(): stdClass
    {
        $model = parent::createNewEntity();
        assert($model instanceof stdClass);

        $model->currency = new Currency('RUB');

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Wallet
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new Wallet(
            $model->name,
            $model->currency,
            $model->useInIncome,
            $model->useInOrder,
            $model->showInLayout,
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
