<?php

declare(strict_types=1);

namespace App\Storage\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Storage\Entity\Warehouse;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseName;
use App\Storage\Entity\WarehouseParent;
use App\Storage\Entity\WarehouseView;
use App\Storage\Form\WarehouseDto;
use function array_map;
use function assert;
use Closure;
use function is_string;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class WarehouseController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity()
    {
        return new WarehouseDto(WarehouseId::generate(), '', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForm($entity, array $entityProperties, $view): \Symfony\Component\Form\FormInterface
    {
        assert($entity instanceof WarehouseDto);

        return $this->createFormBuilder($entity)
            ->add('name', TextType::class, [
                'label' => 'Название',
            ])
            ->add('parentId', ChoiceType::class, [
                'label' => 'Родитель',
                'choice_loader' => new CallbackChoiceLoader(function () use ($entity): array {
                    $ids = $this->registry->connection(WarehouseView::class)
                        ->fetchAll('
                            SELECT id
                            FROM warehouse
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
                            )',
                            [
                                'root' => $entity->id,
                            ],
                            [
                                'root' => 'warehouse_id',
                            ]
                        );

                    return array_map(fn (array $row) => WarehouseId::fromString($row['id']), $ids);
                }),
                'choice_label' => fn (WarehouseId $id) => $this->display($id),
                'choice_value' => fn (?WarehouseId $id) => null === $id ? null : $id->toString(),
                'required' => false,
                'expanded' => true,
            ])
            ->getForm();
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
                WarehouseId::fromString($id)
            )
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

        if ($view->parentId !== $dto->parentId) {
            $em->persist(new WarehouseParent($dto->id, $dto->parentId));
        }

        $em->flush();
    }

    protected function renderTemplate($actionName, $templatePath, array $parameters = [])
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
