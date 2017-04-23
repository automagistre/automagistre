<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Part;
use App\Form\Model\IncomePart;
use JavierEguiluz\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomePartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('part', EasyAdminAutocompleteType::class, [
                'class' => Part::class,
            ])
            ->add('price', MoneyType::class)
            ->add('quantity', QuantityType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => IncomePart::class,
            ]);
    }
}
