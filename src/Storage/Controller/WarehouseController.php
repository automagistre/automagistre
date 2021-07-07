<?php

declare(strict_types=1);

namespace App\Storage\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Storage\Entity\Warehouse;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseName;
use App\Storage\Entity\WarehouseParent;
use App\Storage\Entity\WarehouseView;
use App\Storage\Form\Warehouse\WarehouseTransformer;
use App\Storage\Form\WarehouseDto;
use Closure;
use Generator;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function assert;
use function is_string;
use function str_repeat;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class WarehouseController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): WarehouseDto
    {
        return new WarehouseDto(WarehouseId::generate(), '', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForm($entity, array $entityProperties, $view): FormInterface
    {
        assert($entity instanceof WarehouseDto);

        $fb = $this->createFormBuilder($entity)
            ->add('name', TextType::class, [
                'label' => 'Название',
            ])
            ->add('parentId', ChoiceType::class, [
                'label' => 'Родитель',
                'choice_loader' => new CallbackChoiceLoader(function () use ($entity): iterable {
                    $ids = $this->registry->connection(WarehouseView::class)
                        ->fetchFirstColumn(
                            '
                            SELECT id
                            FROM warehouse_view
                            WHERE id NOT IN (
                                WITH RECURSIVE tree (id) AS (
                                    SELECT id
                                    FROM warehouse_view
                                    WHERE id = :root

                                    UNION ALL

                                    SELECT sub.id
                                    FROM warehouse_view sub
                                             JOIN tree p ON p.id = sub.parent_id
                                )
                                SELECT *
                                FROM tree
                                ORDER BY id
                            )',
                            [
                                'root' => $entity->id,
                            ],
                            [
                                'root' => 'warehouse_id',
                            ],
                        )
                    ;

                    $all = $this->registry->findBy(WarehouseView::class, ['id' => $ids]);

                    $callback = static function (WarehouseView $previous = null) use (&$callback, &$all): Generator {
                        foreach ($all as $key => $current) {
                            if (
                                (null === $previous && null === $current->parent)
                                || (null !== $previous && $previous->id->equals($current->parent?->id))
                            ) {
                                yield $current;

                                yield from $callback($current);

                                unset($all[$key]);
                            }
                        }
                    };

                    foreach ($callback() as $item) {
                        yield $item;
                    }
                }),
                'choice_label' => fn (WarehouseView $view) => str_repeat('  - -  ', $view->depth).$view->name,
                'choice_value' => fn (?WarehouseView $view) => null === $view ? null : $view->id->toString(),
                'required' => false,
                'expanded' => true,
            ])
        ;

        $fb
            ->get('parentId')
            ->addModelTransformer(WarehouseTransformer::create($this->registry))
        ;

        return $fb->getForm();
    }

    protected function persistEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof WarehouseDto);

        $em = $this->em;
        $em->persist(new Warehouse($dto->id));
        $em->persist(new WarehouseName($dto->id, $dto->name));

        if (null !== $dto->parentId) {
            $em->persist(new WarehouseParent($dto->id, $dto->parentId));
        }

        $em->flush();
    }

    protected function createEditDto(Closure $callable): ?object
    {
        $id = $this->request->query->get('id');

        if (!is_string($id)) {
            throw new BadRequestHttpException('id required.');
        }

        return WarehouseDto::from(
            $this->registry->getBy(
                WarehouseView::class,
                WarehouseId::from($id),
            ),
        );
    }

    protected function updateEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof WarehouseDto);

        $em = $this->em;
        $view = $this->registry->getBy(WarehouseView::class, $dto->id);

        if ($view->name !== $dto->name) {
            $em->persist(new WarehouseName($dto->id, $dto->name));
        }

        if ($view->parent?->id !== $dto->parentId) {
            $em->persist(new WarehouseParent($dto->id, $dto->parentId));
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('new' === $actionName) {
            $parameters['content_title'] = 'Новый склад';

            return $this->render('easy_admin/simple.html.twig', $parameters);
        }

        if ('edit' === $actionName) {
            $parameters['content_title'] = 'Редактировать склад';

            return $this->render('easy_admin/simple.html.twig', $parameters);
        }

        return $this->render($templatePath, $parameters);
    }
}
