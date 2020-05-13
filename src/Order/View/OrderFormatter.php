<?php

declare(strict_types=1);

namespace App\Order\View;

use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Infrastructure\Identifier\IdentifierFormatter;
use App\Infrastructure\Identifier\IdentifierFormatterInterface;
use App\Order\Entity\OrderId;
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

        // TODO Добавить вывод с автомобилем/клиентом после перевода Order на uuid

        return sprintf('Заказ #%s', $view['id']);
    }
}
