<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', Type\EmailType::class, [
                'label'              => 'form.email',
                'translation_domain' => 'FOSUserBundle',
            ])
            ->add('firstname', Type\TextType::class, [
                'label'  => 'Имя',
                'mapped' => false,
            ])
            ->add('lastname', Type\TextType::class, [
                'label'  => 'Фамилия',
                'mapped' => false,
            ])
            ->add('plainPassword', Type\RepeatedType::class, [
                'type'            => Type\PasswordType::class,
                'options'         => ['translation_domain' => 'FOSUserBundle'],
                'first_options'   => ['label' => 'form.password'],
                'second_options'  => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
