<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Car\Entity\Car;
use App\Customer\Entity\Operand;
use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Order\Form\Type\OrderItemServiceType;
use App\Shared\Doctrine\Registry;
use function assert;
use function in_array;
use function sprintf;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderPrintController extends AbstractController
{
    public function matchingAction(): Response
    {
        $order = $this->getEntity(Order::class);
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
                : $this->registry->getBy(Operand::class, ['id' => $order->getCustomerId()]),
        ]);
    }

    public function giveOutAction(): Response
    {
        $order = $this->getEntity(Order::class);
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

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if ($order->isClosed() || $order->isReadyToClose()) {
            goto finish;
        }

        $services = $order->getServicesWithoutWorker();

        if ([] !== $services) {
            $form = $this->createForm(CollectionType::class, $services, [
                'label' => false,
                'entry_type' => OrderItemServiceType::class,
            ])
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                goto mileage;
            }

            return $this->render('easy_admin/order/finish.html.twig', [
                'header' => 'Введите исполнителей',
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        mileage:

        $carId = $order->getCarId();
        if (null !== $carId && null === $order->getMileage()) {
            $carView = $this->container->get(Registry::class)->view($carId);
            $mileage = $carView['mileage'];

            $form = $this->createForm(IntegerType::class, null, [
                'label' => 'Пробег '.(0 === $mileage
                        ? '(предыдущий отсутствует)'
                        : sprintf('(предыдущий: %s)', $mileage)),
            ])
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $order->setMileage($form->getData());
                $em->flush();

                goto finish;
            }

            return $this->render('easy_admin/order/finish.html.twig', [
                'header' => 'Введите пробег',
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        finish:

        if ($request->isMethod('POST')) {
            return $this->redirect($request->getUri());
        }


        return $this->render('easy_admin/order_print/final.html.twig', [
            'order' => $order,
        ]);
    }

    public function actAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        return $this->render('easy_admin/order_print/act.html.twig', [
            'order' => $order,
        ]);
    }

    public function invoiceAction(): Response
    {
        $order = $this->getEntity(Order::class);
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
    protected function isActionAllowed($actionName): bool
    {
        if (!in_array($actionName, ['finish', 'act', 'invoice'], true)) {
            $order = $this->request->attributes->get('easyadmin')['item'];
            assert($order instanceof Order);

            return $order->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $order = $parameters['order'];
        assert($order instanceof Order);

        $parameters['car'] = $this->registry->findBy(Car::class, ['id' => $order->getCarId()]);
        $parameters['customer'] = $this->registry->findBy(Operand::class, ['id' => $order->getCustomerId()]);

        return parent::render($view, $parameters, $response);
    }
}
