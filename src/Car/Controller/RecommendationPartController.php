<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Car\Entity\RecommendationPartId;
use App\Car\Form\DTO\RecommendationPartDto;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Part\Entity\PartView;
use App\Part\Form\PartOfferDto;
use App\Part\Form\PartOfferType;
use App\Part\Manager\PartManager;
use App\Vehicle\Entity\VehicleId;
use Doctrine\ORM\EntityManagerInterface;
use function is_string;
use LogicException;
use Ramsey\Uuid\Uuid;
use function sprintf;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
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

        $crosses[$part->toId()->toString()] = $part;

        /** @var FormInterface[] $forms */
        $forms = [];
        foreach ($crosses as $crossId => $cross) {
            $isCurrent = $partId->equal($cross->toId());

            $partOffer = new PartOfferDto();
            $partOffer->partId = $cross->toId();
            $partOffer->quantity = $recommendationPart->quantity;
            $partOffer->price = $isCurrent ? $recommendationPart->getPrice() : $cross->suggestPrice();

            $dto = new RecommendationPartDto();
            $dto->recommendation = $recommendationPart->recommendation;
            $dto->partOffer = $partOffer;

            $formBuilder = $this->createFormBuilder($dto, [
                'action' => $this->generateEasyPath('CarRecommendationPart', 'substitute', [
                    'id' => $recommendationPart->toId()->toString(),
                    'cross' => $crossId,
                    'referer' => $request->query->get('referer'),
                ]),
            ]);
            $forms[$crossId] = $formBuilder
                ->add($formBuilder->create('partOffer', null, [
                    'data_class' => PartOfferDto::class,
                    'compound' => true,
                ])
                ->add('quantity', QuantityType::class)
                ->add('price', MoneyType::class)
                )
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
                    /** @var RecommendationPartDto $dto */
                    $dto = $form->getData();

                    $isCurrent = $dto->partOffer->partId->equal($partId);

                    if ($isCurrent) {
                        $recommendationPart->setPrice($dto->partOffer->price);
                        $recommendationPart->quantity = $dto->partOffer->quantity;
                    } else {
                        $entity = new RecommendationPart(
                            RecommendationPartId::generate(),
                            $dto->recommendation,
                            $dto->partOffer->partId,
                            $dto->partOffer->quantity,
                            $dto->partOffer->price,
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

    protected function newAction(): Response
    {
        $recommendation = $this->getEntity(Recommendation::class);
        if (!$recommendation instanceof Recommendation) {
            throw new LogicException('CarRecommendation required.');
        }

        $partOffer = new PartOfferDto();
        $dto = new RecommendationPartDto();
        $dto->recommendation = $recommendation;
        $dto->partOffer = $partOffer;

        $form = $this->createFormBuilder($dto)
            ->add('partOffer', PartOfferType::class, [
                'vehicleId' => $this->getIdentifier(VehicleId::class),
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->persist(
                new RecommendationPart(
                    RecommendationPartId::generate(),
                    $recommendation,
                    $partOffer->partId,
                    $partOffer->quantity,
                    $partOffer->price,
                )
            );
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => $recommendation->service,
            'form' => $form->createView(),
        ]);
    }

    protected function editAction(): Response
    {
        $recommendationPart = $this->findCurrentEntity();
        if (!$recommendationPart instanceof RecommendationPart) {
            throw new LogicException('RecommendationPart required.');
        }

        $partOffer = new PartOfferDto();
        $partOffer->partId = $recommendationPart->partId;
        $partOffer->price = $recommendationPart->getPrice();
        $partOffer->quantity = $recommendationPart->quantity;

        $dto = new RecommendationPartDto();
        $dto->recommendation = $recommendationPart->recommendation;
        $dto->partOffer = $partOffer;

        $form = $this->createFormBuilder($dto)
            ->add('partOffer', PartOfferType::class, [
                'vehicleId' => $this->getIdentifier(VehicleId::class),
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $recommendationPart->setPrice($partOffer->price);
            $recommendationPart->quantity = $partOffer->quantity;

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => $recommendationPart->recommendation->service,
            'form' => $form->createView(),
            'delete_form' => $this->createDeleteForm(
                $this->entity['name'],
                $recommendationPart->toId()->toString(),
            )->createView(),
        ]);
    }
}
