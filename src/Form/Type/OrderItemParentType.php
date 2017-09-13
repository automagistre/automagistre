<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\OrderItem;
use App\Entity\OrderItemGroup;
use App\Entity\OrderItemPart;
use App\Form\Model\OrderItemModel;
use App\Form\Model\OrderPart as OrderItemPartModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemParentType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $currentItem = $this->requestStack->getMasterRequest()->attributes->get('easyadmin')['item'];

        if ($currentItem instanceof OrderItem) {
            $rootItems = $currentItem->getOrder()->getRootItems();
        } elseif ($currentItem instanceof OrderItemModel) {
            $rootItems = $currentItem->order->getRootItems();
        } else {
            throw new \LogicException(sprintf('Unexpected item of class "%s"', get_class($currentItem)));
        }

        $items = [];
        $appendItem = function (OrderItem $item, $currentItem) use (&$items): void {
            if (null === $currentItem || $this->validParent($currentItem, $item)) {
                $items[] = $item;
            }
        };

        foreach ($rootItems as $item) {
            $appendItem($item, $currentItem);

            foreach ($this->getChildren($item) as $child) {
                $appendItem($child, $currentItem);
            }
        }

        $resolver->setDefaults([
            'choices' => $items,
            'choice_label' => function (OrderItem $item) {
                return str_repeat(' - ', $item->getLevel()).(string) $item;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getChildren(OrderItem $item): array
    {
        $items = [];
        foreach ($item->getChildren() as $child) {
            $items[] = [$child];

            $items[] = $this->getChildren($child);
        }

        if (!$items) {
            return [];
        }

        return 1 < count($items) ? array_merge(...$items) : array_shift($items);
    }

    /**
     * @param OrderItem|OrderItemModel $nestable
     * @param OrderItem                $parent
     *
     * @return bool
     */
    private function validParent($nestable, OrderItem $parent): bool
    {
        if ($parent instanceof OrderItemGroup) {
            if ($nestable instanceof OrderItemPartModel) {
                return true;
            }

            if ($nestable instanceof OrderItemGroup) {
                if ($nestable->getId() === $parent->getId()) {
                    return false;
                }

                $p = $parent;
                while ($p = $p->getParent()) {
                    if ($p->getId() === $nestable->getId()) {
                        return false;
                    }
                }
            }

            return true;
        }

        if ($parent instanceof OrderItemPart) {
            return false;
        }

        if ($nestable instanceof OrderItemPart || $nestable instanceof OrderItemPartModel) {
            return true;
        }

        return false;
    }
}
