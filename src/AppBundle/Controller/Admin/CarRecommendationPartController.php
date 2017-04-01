<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\CarRecommendation;
use AppBundle\Entity\CarRecommendationPart;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
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

        return new CarRecommendationPart($recommendation);
    }
}
