<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\OrderItem;
use App\Entity\OrderItemGroup;
use App\Entity\OrderItemPart;
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
        $currentItem = $this->getCurrentItem();
        $rootItems = $currentItem->getOrder()->getRootItems();

        $items = [];
        foreach ($rootItems as $item) {
            if ($this->validParent($currentItem, $item)) {
                $items[] = $item;
            }

            foreach ($this->getChildren($item) as $child) {
                if ($this->validParent($currentItem, $child)) {
                    $items[] = $child;
                }
            }
        }

        $resolver->setDefaults([
            'choices'      => $items,
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

    private function getCurrentItem(): OrderItem
    {
        $admin = $this->requestStack->getMasterRequest()->attributes->get('easyadmin');

        $item = $admin['item'];
        if (!$item instanceof OrderItem) {
            throw new \LogicException('Item from request.attributes.easyadmin.item is not an OrderItem instance');
        }

        return $item;
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

    private function validParent(OrderItem $nestable, OrderItem $parent): bool
    {
        if ($parent instanceof OrderItemGroup) {
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

        if ($nestable instanceof OrderItemPart) {
            return true;
        }

        return false;
    }
}
