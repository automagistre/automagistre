<?php

declare(strict_types=1);

namespace App\Order\View;

use App\Order\Entity\OrderId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use function sprintf;

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
        $view = $this->registry->view($identifier);

        $suffix = '#'.$view['id'];
        if (null !== $view['carId']) {
            $suffix = 'на '.$formatter->format($view['carId'], 'long');
        } elseif (null !== $view['customerId']) {
            $suffix = 'от '.$formatter->format($view['customerId']);
        }

        return sprintf('Заказ %s', $suffix);
    }
}
