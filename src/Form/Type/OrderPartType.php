<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\OrderItemPart;
use App\Entity\Part;
use JavierEguiluz\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class OrderPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('part', EasyAdminAutocompleteType::class, [
                'class' => Part::class,
                'label' => 'Наименование',
            ])
            ->add('cost', NumberType::class, [
                'required' => true,
                'label' => 'Цена',
            ])
            ->add('quantity', NumberType::class, [
                'required' => true,
                'label' => 'Количество',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => OrderItemPart::class,
            ]);
    }
}
