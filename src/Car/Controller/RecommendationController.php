<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Form\DTO\RecommendationDTO;
use App\Car\Manager\RecommendationManager;
use App\Customer\Entity\Operand;
use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
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

        $recommendation = $this->registry->repository(Recommendation::class)
            ->findOneBy(['id' => $query->get('id')]);

        if (!$recommendation instanceof Recommendation) {
            throw new NotFoundHttpException();
        }

        $this->manager->realize($recommendation, $order);

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

        $car = $this->registry->repository(Car::class)->findOneBy(['id' => $id]);
        if (!$car instanceof Car) {
            throw new BadRequestHttpException(sprintf('Car id "%s" not found', $id));
        }

        $model = new RecommendationDTO($car);

        $order = $this->getEntity(Order::class);
        if ($order instanceof Order) {
            $model->workerId = $order->getWorkerPersonId();
        }

        if (null === $model->workerId) {
            $em = $this->em;
            $result = $em->createQueryBuilder()
                ->select('entity.uuid AS id')
                ->from(Operand::class, 'entity')
                ->join(Recommendation::class, 'cr', Join::WITH, 'entity.uuid = cr.workerId')
                ->where('cr.car = :car')
                ->orderBy('entity.id', 'DESC')
                ->getQuery()
                ->setParameter('car', $car)
                ->setMaxResults(1)
                ->getOneOrNullResult();

            if (null !== $result) {
                $model->workerId = $result['id'];
            }
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

        $entity = new Recommendation(
            $model->car,
            $model->service,
            $model->price,
            $model->workerId,
            $this->getUser()->toId()
        );

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
