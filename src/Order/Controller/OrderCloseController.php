<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Order\Form\Close\OrderCloseDto;
use App\Order\Form\Close\OrderCloseType;
use App\Order\Messages\CloseOrderCommand;
use App\Order\Messages\CreatePayment;
use function sprintf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class OrderCloseController extends AbstractController
{
    public function closeAction(Request $request): Response
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
        $customerId = $order->getCustomerId();

        $dto = new OrderCloseDto($orderId, $order->getCarId());
        $form = $this->createForm(OrderCloseType::class, $dto)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            foreach ($dto->payment->wallets as $walletDto) {
                if (!$walletDto->payment->isPositive()) {
                    continue;
                }

                $this->dispatchMessage(
                    new CreatePayment(
                        $orderId,
                        $customerId,
                        $walletDto->walletId,
                        $walletDto->payment,
                    ),
                );
            }

            $mileageDto = $dto->finish->mileage;
            if (null !== $mileageDto && null !== $mileageDto->mileage) {
                $order->setMileage($mileageDto->mileage);
            }

            $this->dispatchMessage(
                new CloseOrderCommand(
                    $orderId,
                    $dto->feedback->satisfaction,
                )
            );

            return $this->redirectToEasyPath('Order', 'show', ['id' => $orderId->toString()]);
        }

        return $this->render('easy_admin/order/close.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'button' => 'Закрыть',
        ]);
    }
}
