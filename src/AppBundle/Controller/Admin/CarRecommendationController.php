<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Car;
use AppBundle\Entity\CarRecommendation;
use AppBundle\Entity\Order;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarRecommendationController extends AdminController
{
    protected function createNewEntity()
    {
        if (!$id = $this->request->query->get('car_id')) {
            throw new BadRequestHttpException('car_id is required');
        }

        $car = $this->em->getRepository(Car::class)->findOneBy(['id' => $id]);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Car id "%s" not found', $id));
        }

        return new CarRecommendation($car);
    }

    public function realizeAction()
    {
        if (!$this->request->isMethod('POST')) {
            throw new BadRequestHttpException();
        }

        $query = $this->request->query;

        $order = $this->em->getRepository(Order::class)->findOneBy(['id' => $query->get('order_id')]);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        $recommendation = $this->em->getRepository(CarRecommendation::class)->findOneBy(['id' => $query->get('id')]);
        if (!$recommendation) {
            throw new NotFoundHttpException();
        }

        $order->realizeRecommendation($recommendation);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id'     => $order->getId(),
        ]);
    }
}
