<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Order\Messages\CancelOrder;
use function sprintf;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrderCancelController extends AbstractController
{
    public function cancelAction(Request $request): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if (!$order->isEditable()) {
            $this->addFlash('danger', sprintf('Заказ № %s уже закрыт.', $order->getNumber()));

            return $this->redirectToReferrer();
        }

        $orderId = $order->toId();

        $form = $this->createFormBuilder()
            ->add('checkbox', CheckboxType::class, [
                'label' => 'Подтверждаю отмену заказа',
                'required' => true,
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchMessage(
                new CancelOrder(
                    $orderId,
                ),
            );

            return $this->redirectToEasyPath('Order', 'show', ['id' => $orderId->toString()]);
        }

        return $this->render('easy_admin/order/cancel.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'button' => 'Применить',
        ]);
    }
}
