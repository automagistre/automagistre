<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\OrderTOService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('discount', MoneyType::class, [
                'label' => 'Скидка на работу',
                'required' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $model = $event->getData();
                if (!$model instanceof OrderTOService) {
                    throw new \LogicException('OrderTOService expected.');
                }

                if (0 === \count($model->parts)) {
                    return;
                }

                $form->add('parts', CollectionType::class, [
                    'label' => false,
                    'entry_type' => OrderTOPartType::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                ]);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => OrderTOService::class,
            ]);
    }
}
