<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\CarRecommendationPart;
use App\Form\Model\RecommendationPart;
use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Manager\PartManager;
use App\Utils\UrlUtils;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarRecommendationPartController extends AbstractController
{
    /**
     * @var PartManager
     */
    private $partManager;

    public function __construct(PartManager $partManager)
    {
        $this->partManager = $partManager;
    }

    public function substituteAction(): Response
    {
        $request = $this->request;

        $recommendationPart = $this->findCurrentEntity();
        if (!$recommendationPart instanceof CarRecommendationPart) {
            throw new LogicException('CarRecommendationPart required.');
        }

        $part = $recommendationPart->getPart();

        $crosses = $this->partManager->crossesInStock($part);

        if ([] === $crosses) {
            $this->addFlash('error', \sprintf('У запчасти "%s" нет аналогов.', (string) $part));

            return $this->redirectToReferrer();
        }

        \array_unshift($crosses, $part);

        /** @var FormInterface[] $forms */
        $forms = [];
        foreach ($crosses as $cross) {
            $crossId = $cross->getId();

            $isCurrent = $part->getId() === $crossId;

            $model = new RecommendationPart();
            $model->recommendation = $recommendationPart->getRecommendation();
            $model->part = $cross;
            $model->quantity = $recommendationPart->getQuantity();
            $model->price = $isCurrent ? $recommendationPart->getPrice() : $this->partManager->suggestPrice($cross);

            $forms[$crossId] = $this->createFormBuilder($model, [
                'action' => UrlUtils::addQuery($request->getUri(), 'cross', (string) $crossId),
            ])
                ->add('quantity', QuantityType::class)
                ->add('price', MoneyType::class)
                ->getForm();
        }

        $currentForm = $request->query->getAlnum('cross');
        if (\is_numeric($currentForm)) {
            $form = $forms[$currentForm];
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->registry->manager(CarRecommendationPart::class);

                $em->transactional(function (EntityManagerInterface $em) use ($form, $recommendationPart, $part): void {
                    /** @var RecommendationPart $model */
                    $model = $form->getData();

                    $isCurrent = $model->part->getId() === $part->getId();

                    if ($isCurrent) {
                        $recommendationPart->setPrice($model->price);
                        $recommendationPart->setQuantity($model->quantity);
                    } else {
                        $entity = new CarRecommendationPart(
                            $model->recommendation,
                            $model->part,
                            $model->quantity,
                            $model->price,
                            $this->getUser()
                        );

                        $em->persist($entity);
                        $em->remove($recommendationPart);
                    }
                });

                return $this->redirectToReferrer();
            }
        }

        return $this->render('easy_admin/car_recommendation_part/substitute.html.twig', [
            'part' => $part,
            'forms' => $forms,
        ]);
    }

    protected function createNewEntity(): RecommendationPart
    {
        $recommendation = $this->getEntity(CarRecommendation::class);
        if (!$recommendation instanceof CarRecommendation) {
            throw new LogicException('CarRecommendation required.');
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
