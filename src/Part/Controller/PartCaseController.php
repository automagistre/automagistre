<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartId;
use App\Part\Form\PartAutocompleteType;
use App\Part\Form\PartCaseDTO;
use App\Vehicle\Form\VehicleAutocompleteType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartCaseController extends AbstractController
{
    protected function caseAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);

        if (!$partId instanceof PartId) {
            throw new BadRequestHttpException('Part required.');
        }

        $dto = new PartCaseDTO();
        $dto->partId = $partId;

        $form = $this->createFormBuilder($dto)
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => true,
            ])
            ->add('vehicleId', VehicleAutocompleteType::class, [
                'required' => true,
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(PartCase::class);
            $em->persist(new PartCase($dto->partId, $dto->vehicleId));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Добавить кузов',
            'button' => 'Добавить',
            'form' => $form->createView(),
            'entity_fields' => [],
            'entity' => $dto,
        ]);
    }
}
