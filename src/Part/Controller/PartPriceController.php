<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\MoneyType;
use App\Part\Entity\PartId;
use App\Part\Entity\Price;
use App\Part\Form\PartAutocompleteType;
use App\Part\Form\PartPriceDto;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartPriceController extends AbstractController
{
    public function newAction(): Response
    {
        $request = $this->request;

        $dto = new PartPriceDto();
        $dto->partId = $this->getIdentifier(PartId::class);
        $dto->since = new DateTimeImmutable();

        $form = $this->createFormBuilder($dto)
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => true,
            ])
            ->add('price', MoneyType::class)
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist(new Price($dto->partId, $dto->price, $dto->since));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Новая цена',
            'form' => $form->createView(),
        ]);
    }
}
