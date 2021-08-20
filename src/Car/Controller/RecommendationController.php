<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationId;
use App\Car\Form\DTO\RecommendationDTO;
use App\Car\Manager\RecommendationManager;
use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function assert;
use function sprintf;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class RecommendationController extends AbstractController
{
    public function __construct(private RecommendationManager $manager)
    {
    }

    public function realizeAction(): RedirectResponse
    {
        if (!$this->request->isMethod('POST')) {
            throw new BadRequestHttpException();
        }

        $query = $this->request->query;

        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new NotFoundHttpException();
        }

        if (!$order->isEditable()) {
            throw new BadRequestHttpException();
        }

        $recommendation = $this->registry->repository(Recommendation::class)
            ->findOneBy(['id' => $query->get('id')])
        ;

        if (!$recommendation instanceof Recommendation) {
            throw new NotFoundHttpException();
        }

        $this->manager->realize($recommendation, $order);

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $order->toId()->toString(),
        ]);
    }

    protected function createNewEntity(): RecommendationDTO
    {
        $carId = $this->getIdentifierOrNull(CarId::class);

        if (null === $carId) {
            throw new BadRequestHttpException('car_id is required');
        }

        $car = $this->registry->repository(Car::class)->findOneBy(['id' => $carId]);

        if (!$car instanceof Car) {
            throw new BadRequestHttpException(sprintf('Car id "%s" not found', $carId->toString()));
        }

        $model = new RecommendationDTO($car);

        $order = $this->findEntity(Order::class);

        if ($order instanceof Order) {
            $model->workerId = $order->getWorkerPersonId();
        }

        if (null === $model->workerId) {
            $em = $this->em;
            $result = $em->createQueryBuilder()
                ->select('cr.workerId AS id')
                ->from(Recommendation::class, 'cr')
                ->where('cr.car = :car')
                ->orderBy('cr.id', 'DESC')
                ->getQuery()
                ->setParameter('car', $car)
                ->setMaxResults(1)
                ->getOneOrNullResult()
            ;

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
            RecommendationId::generate(),
            $model->car,
            $model->service,
            $model->price,
            $model->workerId,
        );

        parent::persistEntity($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        $parameters['order'] = $this->findEntity(Order::class);

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
