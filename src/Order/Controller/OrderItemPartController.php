<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Car\Entity\Car;
use App\Customer\Form\SellerType;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Order\Exception\ReservationException;
use App\Order\Form\OrderPart;
use App\Order\Form\Type\OrderItemParentType;
use App\Order\Form\Type\WarrantyType;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Form\PartOfferDto;
use App\Part\Form\PartOfferType;
use App\Vehicle\Entity\VehicleId;
use LogicException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemPartController extends OrderItemController
{
    private ReservationManager $reservationManager;

    public function __construct(ReservationManager $reservationManager)
    {
        $this->reservationManager = $reservationManager;
    }

    public function reserveAction(): Response
    {
        $item = $this->getEntity(OrderItemPart::class);
        if (!$item instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }

        try {
            $this->reservationManager->reserve($item);
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToReferrer();
    }

    public function deReserveAction(): Response
    {
        $item = $this->getEntity(OrderItemPart::class);
        if (!$item instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }

        try {
            $this->reservationManager->deReserve($item, $item->getQuantity());
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToReferrer();
    }

    protected function newAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order not found');
        }
        $order = $order->getOrder();
        if (!$order->isEditable()) {
            throw new BadRequestHttpException('Order closed.');
        }

        $partOffer = $this->createWithoutConstructor(PartOfferDto::class);
        $dto = $this->createWithoutConstructor(OrderPart::class);
        $dto->order = $order;
        $dto->partOffer = $partOffer;

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $dto;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $partId = $this->getIdentifier(PartId::class);
        if ($partId instanceof PartId) {
            $partOffer->partId = $partId;
        }

        $parent = $this->getEntity(OrderItem::class);
        if ($parent instanceof OrderItem) {
            $dto->parent = $parent;
        }

        $vehicleId = null;
        $carId = $order->getCarId();
        if (null !== $carId) {
            $car = $this->registry->get(Car::class, $carId);
            $vehicleId = $car->vehicleId;
        }

        $form = $this->createItemForm($dto, $vehicleId)
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $orderItemPart = new OrderItemPart(
                Uuid::uuid6(),
                $order,
                $partOffer->partId,
                $partOffer->quantity
            );
            $orderItemPart->setParent($dto->parent);
            $orderItemPart->setWarranty($dto->warranty);
            $orderItemPart->setSupplierId($dto->supplierId);
            $orderItemPart->setPrice(
                $partOffer->price,
                $this->registry->get(PartView::class, $partOffer->partId),
            );

            $em->persist($orderItemPart);
            $em->flush();

            try {
                $this->reservationManager->reserve($orderItemPart);
            } catch (ReservationException $e) {
                $this->addFlash('warning', $e->getMessage());
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order_item_part/new.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    protected function editAction(): Response
    {
        $entity = $this->findCurrentEntity();
        if (!$entity instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }
        $order = $entity->getOrder();
        if (!$order->isEditable()) {
            throw new BadRequestHttpException('Order closed.');
        }

        $price = $entity->getPrice();
        $discount = $entity->discount();
        if ($discount->isPositive()) {
            $price = $price->subtract($discount);
        }

        $partOffer = new PartOfferDto(
            $entity->getPartId(),
            $entity->getQuantity(),
            $price
        );

        $dto = new OrderPart(
            $order,
            $entity->getParent(),
            $partOffer,
            $entity->isWarranty(),
            $entity->getSupplierId(),
        );

        $vehicleId = null;
        $carId = $order->getCarId();
        if (null !== $carId) {
            $car = $this->registry->get(Car::class, $carId);
            $vehicleId = $car->vehicleId;
        }

        $form = $this->createItemForm($dto, $vehicleId)
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $entity->setParent($dto->parent);
            $entity->setPrice($partOffer->price, $this->registry->get(PartView::class, $partOffer->partId));
            $entity->setQuantity($partOffer->quantity);
            $entity->setWarranty($dto->warranty);
            $entity->setSupplierId($dto->supplierId);

            $em->flush();

            try {
                $this->reservationManager->reserve($entity);
            } catch (ReservationException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order_item_part/edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'delete_form' => $this->createDeleteForm($this->entity['name'], $entity->toId()->toString())->createView(),
        ]);
    }

    private function createItemForm(OrderPart $dto, ?VehicleId $vehicleId): FormInterface
    {
        return $this->createFormBuilder($dto)
            ->add('parent', OrderItemParentType::class, [
                'label' => 'Работа / Группа',
            ])
            ->add('partOffer', PartOfferType::class, [
                'vehicleId' => $vehicleId,
            ])
            ->add('warranty', WarrantyType::class)
            ->add('supplierId', SellerType::class, [
                'required' => false,
            ])
            ->getForm();
    }
}
