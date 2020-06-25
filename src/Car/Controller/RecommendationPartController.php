<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Car\Form\DTO\RecommendationPartDTO;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Part\Entity\PartView;
use App\Part\Manager\PartManager;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use function is_string;
use LogicException;
use Ramsey\Uuid\Uuid;
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
        /** @var PartView $part */
        $part = $this->registry->getBy(PartView::class, ['id' => $partId]);
        /** @var PartView[] $crosses */
        $crosses = $this->partManager->crossesInStock($partId);

        if ([] === $crosses) {
            $this->addFlash('error', sprintf('У запчасти "%s" нет аналогов.', $this->display($partId)));

            return $this->redirectToReferrer();
        }

        /** @var FormInterface[] $forms */
        $forms = [];
        foreach ($crosses as $crossId => $cross) {
            $isCurrent = $partId->equal($cross->toId());

            $model = new RecommendationPartDTO(
                $recommendationPart->recommendation,
                $cross->toId(),
                $recommendationPart->quantity,
                $isCurrent ? $recommendationPart->getPrice() : $cross->suggestPrice()
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

        $currentForm = $request->query->get('cross');
        if (is_string($currentForm) && Uuid::isValid($currentForm)) {
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
            'crosses' => $crosses,
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
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
