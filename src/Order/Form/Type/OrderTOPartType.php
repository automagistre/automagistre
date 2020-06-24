<?php

declare(strict_types=1);

namespace App\Order\Form\Type;

use App\Form\Type\MoneyType;
use App\Order\Form\OrderTOPart;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Manager\PartManager;
use App\Shared\Identifier\IdentifierFormatter;
use function array_map;
use function assert;
use function count;
use LogicException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOPartType extends AbstractType
{
    private PartManager $partManager;

    private IdentifierFormatter $formatter;

    public function __construct(PartManager $partManager, IdentifierFormatter $formatter)
    {
        $this->partManager = $partManager;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selected', CheckboxType::class, [
                'label' => 'Выбрать',
                'required' => false,
            ])
            ->add('price', MoneyType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $data = $event->getData();
                if (!$data instanceof OrderTOPart) {
                    throw new LogicException('OrderTOPart expected.');
                }

                $partId = $data->partId;

                $analogs = $this->partManager->crossesInStock($partId);
                $hasAnalog = 0 < count($analogs);

                $choices = [$partId];
                if ($hasAnalog) {
                    $choices = [...$choices, ...array_map(fn (Part $part) => $part->toId(), $analogs)];
                }

                $form->add('partId', ChoiceType::class, [
                    'label' => 'Запчасть',
                    'choices' => $choices,
                    'choice_label' => fn (PartId $partId) => $this->formatter->format($partId),
                    'choice_value' => fn (PartId $partId) => $partId->toString(),
                    'expanded' => false,
                    'multiple' => false,
                    'disabled' => !$hasAnalog,
                ]);
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $model = $event->getData();
                assert($model instanceof OrderTOPart);

                $price = $form->get('price');
                if (null === $price->getData()) {
                    $price->setData($this->partManager->suggestPrice($model->partId));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => OrderTOPart::class,
            ]);
    }
}
