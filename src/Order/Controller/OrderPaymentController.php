<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Order\Form\Payment\OrderPaymentDto;
use App\Order\Form\Payment\OrderPaymentType;
use App\Order\Messages\CreatePrepay;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class OrderPaymentController extends AbstractController
{
    public function paymentAction(Request $request): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        $dto = new OrderPaymentDto($order->toId());
        $form = $this->createForm(OrderPaymentType::class, $dto)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($dto->wallets as $walletDto) {
                if (!$walletDto->payment->isPositive()) {
                    continue;
                }

                $this->dispatchMessage(
                    new CreatePrepay(
                        $order->toId(),
                        $walletDto->walletId,
                        $walletDto->payment,
                        $dto->description,
                    ),
                );
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/payment.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}
