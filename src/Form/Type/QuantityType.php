<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Transformer\QuantityToLocalizedStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class QuantityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new QuantityToLocalizedStringTransformer(
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
        ]);

        $resolver->setAllowedTypes('scale', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'quantity';
    }
}
