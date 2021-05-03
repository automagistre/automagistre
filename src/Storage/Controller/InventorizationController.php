<?php

declare(strict_types=1);

namespace App\Storage\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\QuantityType;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Storage\Entity\Inventorization;
use App\Storage\Entity\Part;
use App\Storage\Form\Inventory\InventoryDto;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class InventorizationController extends AbstractController
{
    public function inventoryAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);

        $dto = new InventoryDto();

        $form = $this->createFormBuilder($dto)
            ->add('quantity', QuantityType::class)
            ->add('description', TextType::class, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Inventorization::class);

            $storagePart = $this->registry->find(Part::class, $partId);

            if (null === $storagePart) {
                $storagePart = new Part($partId);
                $em->persist($storagePart);
            }

            $storagePart->inventory($dto->quantity, $dto->description);

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/inventory.html.twig', [
            'part' => $this->registry->get(PartView::class, $partId),
            'form' => $form->createView(),
        ]);
    }
}
