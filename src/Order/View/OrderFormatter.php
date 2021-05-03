<?php

declare(strict_types=1);

namespace App\Order\View;

use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class OrderFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return OrderId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $order = $this->registry->get(Order::class, $identifier);

        $string = $order->getNumber();

        if ('number' === $format) {
            return $string;
        }

        $carId = $order->getCarId();
        $customerId = $order->getCustomerId();

        if (null !== $carId) {
            $string = $formatter->format($carId, 'long');
        } elseif (null !== $customerId) {
            $string = $formatter->format($customerId);
        }

        return $string;
    }
}
