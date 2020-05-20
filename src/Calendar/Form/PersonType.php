<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Имя',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
                'required' => false,
            ])
            ->add('telephone', PhoneNumberType::class, [
                'label' => 'Телефон',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event): void {
                if (null === $event->getData()) {
                    return;
                }

                $event->getForm()->setData(
                    (new ReflectionClass(PersonDto::class))->newInstanceWithoutConstructor(),
                );
            });
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
