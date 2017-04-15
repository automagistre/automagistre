<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\CarRecommendation;
use App\Entity\CarRecommendationPart;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarRecommendationPartController extends AdminController
{
    protected function createNewEntity()
    {
        if (!$id = $this->request->query->get('recommendation_id')) {
            throw new BadRequestHttpException('recommendation_id is required');
        }

        $recommendation = $this->em->getRepository(CarRecommendation::class)->findOneBy(['id' => $id]);
        if (!$recommendation) {
            throw new NotFoundHttpException(sprintf('Recommendation id "%s" not found', $id));
        }

        return new CarRecommendationPart($recommendation, $this->getUser());
    }
}
