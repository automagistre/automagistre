<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Part\Entity\Part;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartId;
use App\Part\Form\PartCaseDTO;
use App\Vehicle\Entity\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartCaseController extends AbstractController
{
    protected function caseAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);
        if (!$partId instanceof PartId) {
            throw new BadRequestHttpException('Part required.');
        }

        $dto = $this->createWithoutConstructor(PartCaseDTO::class);
        $dto->partId = $partId;

        $form = $this->createFormBuilder($dto)
            ->add('partId', AutocompleteType::class, [
                'label' => 'Запчасть',
                'class' => Part::class,
                'disabled' => true,
            ])
            ->add('vehicleId', AutocompleteType::class, [
                'label' => 'Модель',
                'class' => Model::class,
                'help' => 'Проивзодитель, Модель, Год, Поколение, Комплектация, Лошадинные силы',
            ])
            ->getForm()
            ->handleRequest($this->request);

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
