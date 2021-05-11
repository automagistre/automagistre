<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Transformer\DivisoredNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class QuantityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new DivisoredNumberTransformer($options['divisor']))
            ->addModelTransformer(new CallbackTransformer(
                fn (?int $quantity) => $quantity,
                fn (?int $quantity) => match (true) {
                    null === $quantity => null,
                    0 > $quantity => throw new TransformationFailedException(invalidMessage: 'Значение не может быть меньше нуля.'),
                    default => $quantity,
                },
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Количество',
            'divisor' => 100,
            'compound' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'quantity';
    }
}
