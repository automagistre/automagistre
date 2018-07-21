<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Enum\OrderStatus;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatusType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'order_status_enum';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return OrderStatus::class;
    }
}
