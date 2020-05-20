<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Car\Entity\Car;
use App\Customer\Domain\Operand;
use App\EasyAdmin\Form\AutocompleteType;
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
            ->add('customerId', AutocompleteType::class, [
                'class' => Operand::class,
                'required' => false,
                'label' => 'Заказчик',
            ])
            ->add('carId', AutocompleteType::class, [
                'class' => Car::class,
                'required' => false,
                'label' => 'Автомобиль',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Комментарий',
            ])
            ->add('workerId', EmployeeUuidType::class, [
                'required' => false,
                'label' => 'Работник',
            ]);

        if ((bool) $options['new_customer']) {
            $builder->add('customer', PersonType::class, [
                'label' => 'Новый заказчик',
                'required' => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderInfoDto::class,
            'error_bubbling' => false,
            'new_customer' => false,
        ])
            ->setAllowedTypes('new_customer', 'bool');
    }
}
