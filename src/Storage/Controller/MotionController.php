<?php

declare(strict_types=1);

namespace App\Storage\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Storage\Entity\MotionSource;
use App\Storage\Entity\Part;
use App\Storage\Form\Motion\MotionDto;
use App\Storage\Form\Motion\MotionType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MotionController extends AbstractController
{
    public function increaseAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);
        $dto = new MotionDto($partId);

        $form = $this->createForm(MotionType::class, $dto)->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Part::class);

            $storagePart = $this->registry->find(Part::class, $partId);

            if (null === $storagePart) {
                $storagePart = new Part($partId);
                $em->persist($storagePart);
            }

            $storagePart->increase(
                $dto->quantity,
                MotionSource::manual($this->getUser()->toId()),
                $dto->description,
            );

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/income.html.twig', [
            'part' => $this->registry->get(PartView::class, $partId),
            'form' => $form->createView(),
        ]);
    }

    public function decreaseAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);
        $dto = new MotionDto($partId);

        $form = $this->createForm(MotionType::class, $dto)->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Part::class);

            $storagePart = $this->registry->find(Part::class, $partId);

            if (null === $storagePart) {
                $storagePart = new Part($partId);
                $em->persist($storagePart);
            }

            $storagePart->decrease(
                $dto->quantity,
                MotionSource::manual($this->getUser()->toId()),
                $dto->description,
            );

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/outcome.html.twig', [
            'part' => $this->registry->get(PartView::class, $partId),
            'form' => $form->createView(),
        ]);
    }

    public function actualizeAction(): Response
    {
        $partId = $this->getIdentifier(PartId::class);
        $dto = new MotionDto($partId);

        $form = $this->createForm(MotionType::class, $dto)->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Part::class);

            $storagePart = $this->registry->find(Part::class, $partId);

            if (null === $storagePart) {
                $storagePart = new Part($partId);
                $em->persist($storagePart);
            }

            $storagePart->actualize(
                $dto->quantity,
                MotionSource::manual($this->getUser()->toId()),
                $dto->description,
            );

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/actualize.html.twig', [
            'part' => $this->registry->get(PartView::class, $partId),
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $request = $this->request;

        $filter = $request->query->get('filter');

        if ('income' === $filter) {
            $qb->andWhere('entity.quantity >= 0');
        } elseif ('outcome' === $filter) {
            $qb->andWhere('entity.quantity <= 0');
        }

        $partId = $this->getIdentifierOrNull(PartId::class);

        if ($partId instanceof PartId) {
            $qb->andWhere('entity.part = :part')
                ->setParameter('part', $partId)
            ;
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('list' === $actionName) {
            $parameters['part'] = $this->getEntity(Part::class);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
