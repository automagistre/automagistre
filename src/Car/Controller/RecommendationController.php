<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Form\DTO\RecommendationDTO;
use App\Car\Manager\RecommendationManager;
use App\Controller\EasyAdmin\AbstractController;
use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Order;
use function assert;
use Doctrine\ORM\Query\Expr\Join;
use function sprintf;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationController extends AbstractController
{
    private RecommendationManager $manager;

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

        $registry = $this->container->get(Registry::class);

        $recommendation = $registry->repository(Recommendation::class)
            ->findOneBy(['id' => $query->get('id')]);

        if (!$recommendation instanceof Recommendation) {
            throw new NotFoundHttpException();
        }

        $this->manager->realize($recommendation, $order, $this->getUser());

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $order->getId(),
        ]);
    }

    protected function createNewEntity(): RecommendationDTO
    {
        if (null === $id = $this->request->query->get('car_id')) {
            throw new BadRequestHttpException('car_id is required');
        }

        $registry = $this->container->get(Registry::class);

        $car = $registry->repository(Car::class)->findOneBy(['id' => $id]);
        if (null === $car) {
            throw new BadRequestHttpException(sprintf('Car id "%s" not found', $id));
        }

        $model = new RecommendationDTO();
        $model->car = $car;

        $order = $this->getEntity(Order::class);
        if ($order instanceof Order) {
            $model->worker = $order->getWorkerPerson();
        }

        if (null === $model->worker) {
            $em = $this->em;
            $model->worker = $em->createQueryBuilder()
                ->select('entity')
                ->from(Operand::class, 'entity')
                ->join(Recommendation::class, 'cr', Join::WITH, 'entity.id = cr.worker')
                ->where('cr.car = :car')
                ->orderBy('entity.id', 'DESC')
                ->getQuery()
                ->setParameter('car', $car)
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Recommendation
    {
        $model = $entity;
        assert($model instanceof RecommendationDTO);

        $entity = new Recommendation($model->car, $model->service, $model->price, $model->worker, $this->getUser());

        parent::persistEntity($entity);

        return $entity;
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
