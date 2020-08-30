<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\MoneyType;
use App\Part\Entity\Discount;
use App\Part\Entity\PartId;
use App\Part\Form\PartAutocompleteType;
use App\Part\Form\PartDiscountDto;
use DateTimeImmutable;
use function is_string;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PartDiscountController extends AbstractController
{
    public function newAction(): Response
    {
        $request = $this->request;

        $partId = $request->query->get('part_id');
        if (!is_string($partId) || !PartId::isValid($partId)) {
            throw new BadRequestHttpException('part_id is not valid uuid.');
        }

        $dto = $this->createWithoutConstructor(PartDiscountDto::class);
        $dto->partId = PartId::fromString($partId);
        $dto->since = new DateTimeImmutable();

        $form = $this->createFormBuilder($dto)
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => true,
            ])
            ->add('price', MoneyType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist(new Discount($dto->partId, $dto->price, $dto->since));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Новая скидка',
            'form' => $form->createView(),
        ]);
    }
}
