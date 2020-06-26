<?php

declare(strict_types=1);

namespace App\Order\View;

use App\Order\Entity\OrderId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
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
        $view = $this->registry->view($identifier);

        $string = $view['number'];
        if ('number' === $format) {
            return $string;
        }

        if (null !== $view['carId']) {
            $string = $formatter->format($view['carId'], 'long');
        } elseif (null !== $view['customerId']) {
            $string = $formatter->format($view['customerId']);
        }

        return $string;
    }
}
