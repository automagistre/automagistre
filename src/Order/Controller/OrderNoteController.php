<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Order\Entity\Order;
use App\Order\Entity\OrderNote;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderNoteController extends AbstractController
{
    protected function createNewEntity(): OrderNote
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        return new OrderNote($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        $parameters['order'] = $this->getEntity(Order::class);

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
