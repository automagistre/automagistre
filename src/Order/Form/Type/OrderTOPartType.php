<?php

declare(strict_types=1);

namespace App\Order\Form\Type;

use App\Doctrine\Registry;
use App\Form\Type\MoneyType;
use App\Order\Form\OrderTOPart;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use LogicException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function assert;
use function count;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOPartType extends AbstractType
{
    public function __construct(private Registry $registry)
    {
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder): void {
                $form = $event->getForm();
                $data = $event->getData();

                if (!$data instanceof OrderTOPart) {
                    throw new LogicException('OrderTOPart expected.');
                }

                $part = $this->registry->get(PartView::class, $data->partId);

                $analogs = $part->analogs->toArray();
                $hasAnalog = 0 < count($analogs);

                $choices = [$part];

                if ($hasAnalog) {
                    $choices = [...$choices, ...array_values($analogs)];
                }

                $partForm = $builder->create('partId', ChoiceType::class, [
                    'label' => 'Запчасть',
                    'choices' => $choices,
                    'choice_label' => fn (PartView $part) => $part->displayWithStock(),
                    'choice_value' => fn (?PartView $part) => $part?->toId()->toString(),
                    'expanded' => false,
                    'multiple' => false,
                    'disabled' => !$hasAnalog,
                    'auto_initialize' => false,
                ])->addModelTransformer(new CallbackTransformer(
                    fn (?PartId $partId) => null === $partId ? null : $this->registry->get(PartView::class, $partId),
                    fn (?PartView $partView) => $partView?->toId(),
                ));

                $form->add($partForm->getForm());
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $model = $event->getData();
                assert($model instanceof OrderTOPart);

                $price = $form->get('price');

                if (null === $price->getData()) {
                    $part = $this->registry->get(PartView::class, $model->partId);

                    $price->setData($part->suggestPrice());
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => OrderTOPart::class,
            ])
        ;
    }
}
