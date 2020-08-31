<?php

declare(strict_types=1);

namespace App\Customer\Form;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'required' => true,
                'label' => 'Имя',
            ])
            ->add('lastName', null, [
                'label' => 'Фамилия',
                'required' => false,
            ])
            ->add('telephone', PhoneNumberType::class, [
                'label' => 'Телефон',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Электронная почта',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonDto::class,
        ]);
    }
}
