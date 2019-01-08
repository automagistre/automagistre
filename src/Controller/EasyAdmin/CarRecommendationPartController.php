<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\CarRecommendationPart;
use App\Form\Model\RecommendationPart;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarRecommendationPartController extends AbstractController
{
    protected function createNewEntity(): RecommendationPart
    {
        if (null === $id = $this->request->query->get('recommendation_id')) {
            throw new BadRequestHttpException('recommendation_id is required');
        }

        $recommendation = $this->em->getRepository(CarRecommendation::class)->findOneBy(['id' => $id]);
        if (null === $recommendation) {
            throw new NotFoundHttpException(\sprintf('Recommendation id "%s" not found', $id));
        }

        $model = new RecommendationPart();
        $model->recommendation = $recommendation;

        return $model;
    }

    /**
     * @param RecommendationPart $model
     */
    protected function persistEntity($model): void
    {
        $entity = new CarRecommendationPart(
            $model->recommendation,
            $model->part,
            $model->quantity,
            $model->price,
            $this->getUser()
        );

        parent::persistEntity($entity);
    }
}
