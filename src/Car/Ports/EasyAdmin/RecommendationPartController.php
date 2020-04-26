<?php

declare(strict_types=1);

namespace App\Car\Ports\EasyAdmin;

use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Car\Form\DTO\RecommendationPartDTO;
use App\Controller\EasyAdmin\AbstractController;
use App\Doctrine\Registry;
use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Manager\PartManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use function array_unshift;
use function assert;
use function is_numeric;
use function sprintf;

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

        $part = $recommendationPart->part;

        $crosses = $this->partManager->crossesInStock($part);

        if ([] === $crosses) {
            $this->addFlash('error', sprintf('У запчасти "%s" нет аналогов.', (string) $part));

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
                $cross,
                $recommendationPart->quantity,
                $isCurrent ? $recommendationPart->getPrice() : $this->partManager->suggestPrice($cross)
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
                $registry = $this->container->get(Registry::class);

                $em = $registry->manager(RecommendationPart::class);

                $em->transactional(function (EntityManagerInterface $em) use ($form, $recommendationPart, $part): void {
                    /** @var RecommendationPartDTO $model */
                    $model = $form->getData();

                    $isCurrent = $model->part->getId() === $part->getId();

                    if ($isCurrent) {
                        $recommendationPart->setPrice($model->price);
                        $recommendationPart->quantity = $model->quantity;
                    } else {
                        $entity = new RecommendationPart(
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
            $model->part,
            $model->quantity,
            $model->price,
            $this->getUser()
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
