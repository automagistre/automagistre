<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Transformer\DivisoredNumberToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class MoneyType extends \Symfony\Component\Form\Extension\Core\Type\MoneyType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new DivisoredNumberToLocalizedStringTransformer(
                $options['scale'],
                $options['grouping'],
                null,
                $options['divisor']
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'scale'    => 2,
            'grouping' => false,
            'divisor'  => 100,
            'compound' => false,
            'currency' => 'RUB',
        ]);

        $resolver->setAllowedTypes('scale', 'int');
    }
}
