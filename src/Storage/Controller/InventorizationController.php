<?php

declare(strict_types=1);

namespace App\Storage\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Storage\Entity\Inventorization;
use App\Storage\Entity\InventorizationPart;
use App\Storage\Entity\InventorizationPartView;
use App\Storage\Entity\InventorizationView;
use App\Storage\Form\Inventorization\InventorizationPartDto;
use App\Storage\Form\Inventorization\InventorizationPartType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function assert;
use function in_array;
use function sprintf;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class InventorizationController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if (in_array($actionName, ['close', 'delete'], true)) {
            $entity = $this->findCurrentEntity();
            assert($entity instanceof Inventorization);

            if ($entity->isClosed()) {
                return false;
            }
        }

        return parent::isActionAllowed($actionName);
    }

    public function addPartAction(): Response
    {
        $inventorization = $this->findCurrentEntity();
        assert($inventorization instanceof Inventorization);

        $dto = new InventorizationPartDto($inventorization->toId());

        $form = $this->createForm(InventorizationPartType::class, $dto)->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(InventorizationPart::class);

            $em->persist(
                new InventorizationPart(
                    $dto->inventorizationId,
                    $dto->partId,
                    $dto->quantity,
                ),
            );

            try {
                $this->em->flush();
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('error', sprintf('Запчасть "%s" уже добавлена в инвентаризацию.', $this->display($dto->partId)));
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/inventorization/part_add.html.twig', [
            'inventorization' => $inventorization,
            'form' => $form->createView(),
        ]);
    }

    public function editPartAction(): Response
    {
        $inventorization = $this->findCurrentEntity();
        assert($inventorization instanceof Inventorization);

        /** @var InventorizationPart $entity */
        $entity = $this->registry->get(InventorizationPart::class, [
            'inventorizationId' => $inventorization->toId(),
            'partId' => $this->getIdentifier(PartId::class),
        ]);

        $dto = new InventorizationPartDto($entity->inventorizationId);
        $dto->partId = $entity->partId;
        $dto->quantity = $entity->quantity;

        $form = $this->createForm(InventorizationPartType::class, $dto, [
            'part_disabled' => true,
        ])
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $entity->quantity = $dto->quantity;

            $this->em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/inventorization/part_edit.html.twig', [
            'entity' => $entity,
            'inventorization' => $inventorization,
            'form' => $form->createView(),
        ]);
    }

    public function removePartAction(): Response
    {
        $inventorization = $this->findCurrentEntity();
        assert($inventorization instanceof Inventorization);

        /** @var InventorizationPart $entity */
        $entity = $this->registry->get(InventorizationPart::class, [
            'inventorizationId' => $inventorization->toId(),
            'partId' => $this->getIdentifier(PartId::class),
        ]);

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(InventorizationPart::class);
            $em->remove($entity);
            $this->em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/inventorization/part_remove.html.twig', [
            'entity' => $entity,
            'inventorization' => $inventorization,
            'form' => $form->createView(),
        ]);
    }

    public function closeAction(): Response
    {
        $entity = $this->findCurrentEntity();
        assert($entity instanceof Inventorization);

        $anyPart = $this->registry->findOneBy(InventorizationPart::class, ['inventorizationId' => $entity->toId()]);

        if (null === $anyPart) {
            $this->addFlash('error', 'Нельзя провести инвентаризацию без запчастей.');

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Inventorization::class);

            $entity->close();

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/inventorization/close.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    public function leftoversAction(): Response
    {
        $entity = $this->findCurrentEntity();
        assert($entity instanceof Inventorization);

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(Inventorization::class);

            $em->getConnection()
                ->executeQuery(
                    <<<'SQL'
                    INSERT INTO inventorization_part (part_id, inventorization_id, quantity)
                    SELECT id, :id, 0
                    FROM part_view
                    WHERE quantity > 0
                    AND id NOT IN (
                        SELECT part_id FROM inventorization_part WHERE inventorization_id = :id
                    )
                    SQL,
                    [
                        'id' => $entity->toId()->toString(),
                    ],
                )
            ;

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/storage/inventorization/leftovers.html.twig', [
            'entity' => $entity,
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
    ): \Doctrine\ORM\QueryBuilder {
        $config = $this->entity;
        $config['class'] = InventorizationView::class;

        return $this->get('easyadmin.query_builder')
            ->createListQueryBuilder($config, $sortField, $sortDirection, $dqlFilter)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Inventorization);

        parent::persistEntity($entity);

        $this->setReferer(
            $this->generateEasyPath('Inventorization', 'show', ['id' => $entity->toId()->toString()]),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate(
        $actionName,
        $templatePath,
        array $parameters = [],
    ): Response {
        if ('show' === $actionName) {
            /** @var Inventorization $entity */
            $entity = $parameters['entity'];

            /** @var InventorizationPartView[] $items */
            $items = $this->registry->findBy(
                InventorizationPartView::class,
                ['inventorizationId' => $entity->toId()->toString()],
                [
                    'partId' => 'ASC',
                ],
            );
            $parameters['items'] = $items;
            $parameters['parts'] = $this->registry->manager()
                ->createQueryBuilder()
                ->select('t')
                ->from(PartView::class, 't', 't.id')
                ->where('t.id IN (:ids)')
                ->getQuery()
                ->setParameters([
                    'ids' => array_map(static fn (InventorizationPartView $view) => $view->partId, $items),
                ])
                ->getResult()
            ;

            $templatePath = $entity->isClosed()
                ? 'easy_admin/storage/inventorization/show_closed.html.twig'
                : 'easy_admin/storage/inventorization/show_opened.html.twig';
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
