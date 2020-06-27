<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\EasyAdmin\Form\AutocompleteType;
use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Part\Entity\Part;
use App\Vehicle\Entity\VehicleId;
use function assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PartOfferType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (PreSetDataEvent $event) use ($options): void {
                $data = $event->getData();
                assert($data instanceof PartOfferDto);

                $event->getForm()
                    ->add('partId', AutocompleteType::class, [
                        'label' => 'Запчасть',
                        'class' => Part::class,
                        'disabled' => (bool) $data->partId,
                        'autocomplete_parameters' => [
                            'vehicle_id' => null === $options['vehicleId'] ? null : (string) $options['vehicleId'],
                        ],
                    ])
                    ->add('price', MoneyType::class)
                    ->add('quantity', QuantityType::class);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => false,
                'data_class' => PartOfferDto::class,
                'vehicleId' => null,
            ])
            ->setAllowedTypes('vehicleId', [VehicleId::class, 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (null !== $options['vehicleId']) {
            $view->vars['vehicleId'] = $options['vehicleId'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'part_offer';
    }
}
