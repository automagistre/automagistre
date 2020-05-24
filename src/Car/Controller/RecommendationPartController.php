<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Car\Form\DTO\RecommendationPartDTO;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Part\Manager\PartManager;
use function array_unshift;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use function is_numeric;
use LogicException;
use function sprintf;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationPartController extends AbstractController
{
    private PartManager $partManager;

    public function __construct(PartManager $partManager)
    {
        $this->partManager = $partManager;
    }

    public function substituteAction(): Response
    {
        $request = $this->request;

        $recommendationPart = $this->findCurrentEntity();
        if (!$recommendationPart instanceof RecommendationPart) {
            throw new LogicException('CarRecommendationPart required.');
        }

        $partId = $recommendationPart->partId;

        $part = $this->partManager->byId($partId);

        $crosses = $this->partManager->crossesInStock($partId);

        if ([] === $crosses) {
            $this->addFlash('error', sprintf('У запчасти "%s" нет аналогов.', $this->display($partId)));

            return $this->redirectToReferrer();
        }

        array_unshift($crosses, $part);

        /** @var FormInterface[] $forms */
        $forms = [];
        foreach ($crosses as $cross) {
            $crossId = $cross->getId();

            $isCurrent = $part->getId() === $crossId;

            $model = new RecommendationPartDTO(
                $recommendationPart->recommendation,
                $cross->toId(),
                $recommendationPart->quantity,
                $isCurrent ? $recommendationPart->getPrice() : $this->partManager->suggestPrice($cross->toId())
            );

            $forms[$crossId] = $this->createFormBuilder($model, [
                'action' => $this->generateEasyPath($recommendationPart, 'substitute', [
                    'cross' => $crossId,
                    'referer' => $request->query->get('referer'),
                ]),
            ])
                ->add('quantity', QuantityType::class)
                ->add('price', MoneyType::class)
                ->getForm();
        }

        $currentForm = $request->query->getAlnum('cross');
        if (is_numeric($currentForm)) {
            $form = $forms[$currentForm];
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->registry->manager(RecommendationPart::class);

                $em->transactional(function (EntityManagerInterface $em) use (
                    $form,
                    $recommendationPart,
                    $partId
                ): void {
                    /** @var RecommendationPartDTO $model */
                    $model = $form->getData();

                    $isCurrent = $model->partId->equal($partId);

                    if ($isCurrent) {
                        $recommendationPart->setPrice($model->price);
                        $recommendationPart->quantity = $model->quantity;
                    } else {
                        $entity = new RecommendationPart(
                            $model->recommendation,
                            $model->partId,
                            $model->quantity,
                            $model->price,
                            $this->getUser()->toId()
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

    protected function createNewEntity(): RecommendationPartDTO
    {
        $recommendation = $this->getEntity(Recommendation::class);
        if (!$recommendation instanceof Recommendation) {
            throw new LogicException('CarRecommendation required.');
        }

        return new RecommendationPartDTO($recommendation);
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): RecommendationPart
    {
        $model = $entity;
        assert($model instanceof RecommendationPartDTO);

        $entity = new RecommendationPart(
            $model->recommendation,
            $model->partId,
            $model->quantity,
            $model->price,
            $this->getUser()->toId()
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
