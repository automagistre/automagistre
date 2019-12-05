<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Car;
use App\Entity\Tenant\Order;
use App\Form\Type\OrderItemServiceType;
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

        $car = $order->getCar();
        if (!$car instanceof Car) {
            throw new BadRequestHttpException('Car required.');
        }

        return $this->render('easy_admin/order_print/matching_v2.html.twig', [
            'order' => $order,
        ]);
    }

    public function giveOutAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        return $this->render('easy_admin/order_print/give_out_v2.html.twig', [
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

        $car = $order->getCar();
        if (null !== $car && null === $order->getMileage()) {
            $mileage = $car->getMileage();

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

        $version = $request->query->getInt('version');
        $template = sprintf('easy_admin/order_print/final_v%s.html.twig', $version);

        return $this->render($template, [
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
}
