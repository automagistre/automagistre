<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\Customer\Form\SellerType;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\QuantityType;
use App\Part\Entity\PartId;
use App\Part\Entity\Supply;
use App\Part\Enum\SupplySource;
use App\Part\Form\PartAutocompleteType;
use App\Part\Form\SupplyDto;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

final class SupplyController extends AbstractController
{
    public function newAction(): Response
    {
        $request = $this->request;

        $partId = $this->getIdentifier(PartId::class);
        if (!$partId instanceof PartId) {
            throw new BadRequestException('PartId required.');
        }

        $dto = $this->createWithoutConstructor(SupplyDto::class);
        $dto->partId = $partId;

        $form = $this->createFormBuilder($dto)
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => true,
            ])
            ->add('supplierId', SellerType::class, [
                'required' => true,
            ])
            ->add('quantity', QuantityType::class, [
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist(
                new Supply(
                    $dto->partId,
                    $dto->supplierId,
                    $dto->quantity,
                    SupplySource::manual(),
                    $this->getUser()->toId()->toUuid(),
                )
            );
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Новая поставка',
            'form' => $form->createView(),
        ]);
    }
}
