<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\OrderGroup as OrderItemGroupModel;
use App\Form\Model\OrderItemModel;
use App\Form\Model\OrderPart as OrderItemPartModel;
use App\Form\Model\OrderService as OrderItemServiceModel;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemGroup;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function get_class;
use LogicException;
use function sprintf;
use function str_repeat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemParentType extends AbstractType
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => OrderItem::class,
            'query_builder' => function (EntityRepository $repository): QueryBuilder {
                $request = $this->requestStack->getMasterRequest();
                $currentItem = $request instanceof Request ? $request->attributes->get('easyadmin')['item'] : null;

                $qb = $repository->createQueryBuilder('entity')
                    ->where('entity.order = :order');
                $expr = $qb->expr();

                if ($currentItem instanceof OrderItem) {
                    $qb->setParameter('order', $currentItem->getOrder());
                } elseif ($currentItem instanceof OrderItemModel) {
                    $qb->setParameter('order', $currentItem->order);
                }

                if ($currentItem instanceof OrderItemGroup || $currentItem instanceof OrderItemGroupModel) {
                    return $qb->where($expr->isNull('entity.id'));
                }

                $orExpr = [];

                $qb
                    ->leftJoin(OrderItemGroup::class, 'groups', Join::WITH, 'entity.id = groups.id');
                $orExpr[] = $expr->isNotNull('groups.id');

                if ($currentItem instanceof OrderItemService || $currentItem instanceof OrderItemServiceModel) {
                    return $qb->andWhere($expr->orX(...$orExpr));
                }

                $qb
                    ->leftJoin(OrderItemService::class, 'service', Join::WITH, 'entity.id = service.id');
                $orExpr[] = $expr->isNotNull('service.id');

                if ($currentItem instanceof OrderItemPart || $currentItem instanceof OrderItemPartModel) {
                    return $qb->andWhere($expr->orX(...$orExpr));
                }

                throw new LogicException(sprintf('Unsupported currentItem "%s"', get_class($currentItem)));
            },
            'choice_label' => fn (OrderItem $item) => str_repeat(' - ', $item->getLevel()).$item,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }
}
