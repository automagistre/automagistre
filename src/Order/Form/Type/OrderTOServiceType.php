<?php

declare(strict_types=1);

namespace App\Order\Form\Type;

use App\Form\Type\MoneyType;
use App\Order\Form\OrderTOService;
use LogicException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function count;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderTOServiceType extends AbstractType
{
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
            ->add('price', MoneyType::class, [
                'label' => 'Стоимость работы',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $model = $event->getData();

                if (!$model instanceof OrderTOService) {
                    throw new LogicException('OrderTOService expected.');
                }

                if (0 === count($model->parts)) {
                    return;
                }

                $form->add('parts', CollectionType::class, [
                    'label' => false,
                    'entry_type' => OrderTOPartType::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                ]);
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
                'data_class' => OrderTOService::class,
            ])
        ;
    }
}
