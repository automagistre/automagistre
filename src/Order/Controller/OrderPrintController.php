<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Car\Entity\Car;
use App\Customer\Entity\CustomerView;
use App\Customer\Entity\Organization;
use App\EasyAdmin\Controller\AbstractController;
use App\Note\Entity\NoteView;
use App\Order\Entity\Order;
use App\Order\Form\Finish\OrderFinishDto;
use App\Order\Form\Finish\OrderFinishType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrderPrintController extends AbstractController
{
    public function matchingAction(): Response
    {
        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required.');
        }

        return $this->render('easy_admin/order_print/matching.html.twig', [
            'order' => $order,
            'car' => null === $order->getCarId()
                ? null
                : $this->registry->getBy(Car::class, ['id' => $order->getCarId()]),
            'customer' => null === $order->getCustomerId()
                ? null
                : $this->registry->getBy(CustomerView::class, ['id' => $order->getCustomerId()]),
        ]);
    }

    public function giveOutAction(): Response
    {
        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        return $this->render('easy_admin/order_print/give_out.html.twig', [
            'order' => $order,
        ]);
    }

    public function finishAction(): Response
    {
        $em = $this->em;
        $request = $this->request;

        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if (!$order->isClosed() && !$order->isReadyToClose()) {
            $dto = new OrderFinishDto(
                $order->toId(),
                $order->getCarId(),
            );

            $form = $this->createForm(OrderFinishType::class, $dto)->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $mileageDto = $dto->mileage;

                if (null !== $mileageDto && null !== $mileageDto->mileage) {
                    /** @var Car $car */
                    $car = $this->registry->get(Car::class, $order->getCarId());
                    $car->setMileage($mileageDto->mileage);
                    $order->setMileage($mileageDto->mileage);
                }

                $em->flush();
            } else {
                return $this->render('easy_admin/order/finish.html.twig', [
                    'header' => 'Введите исполнителей',
                    'order' => $order,
                    'form' => $form->createView(),
                ]);
            }
        }

        if ($request->isMethod('POST')) {
            return $this->redirect($request->getUri());
        }

        return $this->render('easy_admin/order_print/final.html.twig', [
            'order' => $order,
        ]);
    }

    public function updAction(): Response
    {
        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        return $this->render('easy_admin/order_print/upd.html.twig', [
            'order' => $order,
        ]);
    }

    public function invoiceAction(): Response
    {
        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        return $this->render('easy_admin/order_print/invoice.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $order = $parameters['order'];
        assert($order instanceof Order);

        $parameters['car'] = $this->registry->findOneBy(Car::class, ['id' => $order->getCarId()]);
        $parameters['customer'] = $this->registry->findOneBy(CustomerView::class, ['id' => $order->getCustomerId()]);
        $parameters['requisite'] = $this->registry->findOneBy(Organization::class, ['id' => $order->getCustomerId()])?->requisite;
        $parameters['notes'] = $this->registry->findBy(NoteView::class, [
            'subject' => $order->toId()->toUuid(),
            'isPublic' => true,
        ], ['id' => 'DESC']);

        return parent::render($view, $parameters, $response);
    }
}
