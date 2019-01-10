<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Order;
use App\Form\Model\Recommendation;
use App\Manager\RecommendationManager;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarRecommendationController extends AbstractController
{
    /**
     * @var RecommendationManager
     */
    private $manager;

    public function __construct(RecommendationManager $manager)
    {
        $this->manager = $manager;
    }

    public function realizeAction(): RedirectResponse
    {
        if (!$this->request->isMethod('POST')) {
            throw new BadRequestHttpException();
        }

        $query = $this->request->query;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new NotFoundHttpException();
        }

        if (!$order->isEditable()) {
            throw new BadRequestHttpException();
        }

        $recommendation = $this->registry->repository(CarRecommendation::class)
            ->findOneBy(['id' => $query->get('id')]);

        if (!$recommendation instanceof CarRecommendation) {
            throw new NotFoundHttpException();
        }

        $this->manager->realize($recommendation, $order, $this->getUser());

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $order->getId(),
        ]);
    }

    protected function createNewEntity(): Recommendation
    {
        if (null === $id = $this->request->query->get('car_id')) {
            throw new BadRequestHttpException('car_id is required');
        }

        $car = $this->registry->repository(Car::class)->findOneBy(['id' => $id]);
        if (null === $car) {
            throw new NotFoundHttpException(\sprintf('Car id "%s" not found', $id));
        }

        $model = new Recommendation();
        $model->car = $car;

        $order = $this->getEntity(Order::class);
        if ($order instanceof Order) {
            $model->worker = $order->getActiveWorker();
        }

        if (null === $model->worker) {
            $em = $this->em;
            $model->worker = $em->createQueryBuilder()
                ->select('entity')
                ->from(Operand::class, 'entity')
                ->join(CarRecommendation::class, 'cr', Join::WITH, 'entity.id = cr.worker')
                ->where('cr.car = :car')
                ->orderBy('entity.id', 'DESC')
                ->getQuery()
                ->setParameters([
                    'car' => $car,
                ])
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }

        return $model;
    }

    /**
     * @param Recommendation $model
     */
    protected function persistEntity($model): void
    {
        $entity = new CarRecommendation($model->car, $model->service, $model->price, $model->worker, $this->getUser());

        parent::persistEntity($entity);
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
