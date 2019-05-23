<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Embeddable\Requisite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RequisiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bank', TextType::class, [
                'label' => 'Банк',
            ])
            ->add('legalAddress', TextType::class, [
                'label' => 'Юридический адрес',
            ])
            ->add('inn', TextType::class, [
                'label' => 'ИНН',
            ])
            ->add('kpp', TextType::class, [
                'label' => 'КПП',
            ])
            ->add('ogrn', TextType::class, [
                'label' => 'ОГРН',
            ])
            ->add('ks', TextType::class, [
                'label' => 'Корреспондетский счёт',
            ])
            ->add('rs', TextType::class, [
                'label' => 'Рассчётный счёт',
            ])
            ->add('bik', TextType::class, [
                'label' => 'БИК',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Requisite::class,
                'label' => false,
            ]);
    }
}
