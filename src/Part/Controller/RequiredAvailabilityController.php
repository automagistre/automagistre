<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Form\Type\QuantityType;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Entity\RequiredAvailability;
use App\Part\Form\RequiredAvailabilityDto;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

final class RequiredAvailabilityController extends AbstractController
{
    public function newAction(): Response
    {
        $request = $this->request;

        $partId = $this->getIdentifier(PartId::class);
        if (!$partId instanceof PartId) {
            throw new BadRequestException('PartId required.');
        }
        $partView = $this->registry->get(PartView::class, $partId);

        $dto = $this->createWithoutConstructor(RequiredAvailabilityDto::class);
        $dto->partId = $partId;
        $dto->orderFromQuantity = $partView->orderFromQuantity;
        $dto->orderUpToQuantity = $partView->orderUpToQuantity;

        $form = $this->createFormBuilder($dto)
            ->add('partId', AutocompleteType::class, [
                'class' => Part::class,
                'label' => 'Запчасть',
                'disabled' => true,
            ])
            ->add('orderUpToQuantity', QuantityType::class, [
                'label' => 'Заказывать до количества',
            ])
            ->add('orderFromQuantity', QuantityType::class, [
                'label' => 'Если количество ниже',
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist(new RequiredAvailability($dto->partId, $dto->orderFromQuantity, $dto->orderUpToQuantity));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Новое значение Доступности',
            'form' => $form->createView(),
        ]);
    }
}
