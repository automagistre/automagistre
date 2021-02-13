<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\QuantityType;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartDecreased;
use App\Storage\Entity\Motion;
use App\Storage\Enum\Source;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use function abs;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartIncomeController extends AbstractController
{
    public function incomeAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);

        if (!$partId instanceof PartId) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('quantity', QuantityType::class)
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Motion::class);
            $quantity = (int) abs($form->get('quantity')->getData());
            $user = $this->getUser();

            $em->persist(new Motion($partId, $quantity, Source::manual(), $user->toId()->toUuid()));
            $em->flush();

            $this->event(new PartAccrued($partId, [
                'quantity' => $quantity,
            ]));

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/income.html.twig', [
            'part' => $this->registry->get(PartView::class, $partId),
            'form' => $form->createView(),
        ]);
    }

    public function outcomeAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);

        if (!$partId instanceof PartId) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('quantity', QuantityType::class)
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Motion::class);
            $quantity = (int) abs($form->get('quantity')->getData());
            $user = $this->getUser();

            $em->persist(new Motion($partId, 0 - $quantity, Source::manual(), $user->toId()->toUuid()));
            $em->flush();

            $this->event(new PartDecreased($partId, [
                'quantity' => $quantity,
            ]));

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/outcome.html.twig', [
            'part' => $this->registry->get(PartView::class, $partId),
            'form' => $form->createView(),
        ]);
    }
}
