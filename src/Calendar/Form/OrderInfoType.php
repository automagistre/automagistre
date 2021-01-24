<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Car\Form\CarAutocompleteType;
use App\Customer\Form\CustomerAutocompleteType;
use App\Employee\Form\EmployeeUuidType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderInfoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerId', CustomerAutocompleteType::class, [
                'disabled' => $options['disable_customer_and_car'],
            ])
            ->add('carId', CarAutocompleteType::class, [
                'disabled' => $options['disable_customer_and_car'],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Комментарий',
            ])
            ->add('workerId', EmployeeUuidType::class, [
                'required' => false,
                'label' => 'Работник',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderInfoDto::class,
            'error_bubbling' => false,
            'disable_customer_and_car' => false,
        ]);
    }
}
