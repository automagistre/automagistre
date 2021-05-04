<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\Customer\Entity\OperandId;
use App\Customer\Form\SellerType;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\QuantityType;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Entity\Supply;
use App\Part\Enum\SupplySource;
use App\Part\Form\SupplyDto;
use App\Part\Form\PartAutocompleteType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class SupplyController extends AbstractController
{
    public function increaseAction(): Response
    {
        $request = $this->request;

        $dto = new SupplyDto();
        $dto->partId = $this->getIdentifier(PartId::class);

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
            ->handleRequest($request)
        ;

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

    public function decreaseAction(): Response
    {
        $request = $this->request;

        $dto = new SupplyDto();
        $dto->partId = $this->getIdentifier(PartId::class);
        $dto->supplierId = $this->getIdentifier(OperandId::class, 'supplier_id');

        $form = $this->createFormBuilder($dto)
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => true,
            ])
            ->add('supplierId', SellerType::class, [
                'required' => true,
                'disabled' => true,
            ])
            ->add('quantity', QuantityType::class, [
                'constraints' => new Callback(
                    function (int $value, ExecutionContextInterface $context) use ($dto): void {
                        $partView = $this->registry->get(PartView::class, $dto->partId);

                        $supplyView = null;
                        foreach ($partView->supplies() as $supply) {
                            if ($supply->supplierId->equals($dto->supplierId)) {
                                $supplyView = $supply;

                                break;
                            }
                        }

                        if (null !== $supplyView && $supplyView->quantity < $value) {
                            $context->addViolation(
                                'Ожидается всего "{{ supplies }}", невозможно убавить на "{{ decrease }}".',
                                [
                                    '{{ supplies }}' => $partView->suppliesQuantity,
                                    '{{ decrease }}' => $value,
                                ]
                            );
                        }
                    },
                ),
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist(
                new Supply(
                    $dto->partId,
                    $dto->supplierId,
                    0 - $dto->quantity,
                    SupplySource::manual(),
                    $this->getUser()->toId()->toUuid(),
                )
            );
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Убавить поставку',
            'form' => $form->createView(),
        ]);
    }
}
