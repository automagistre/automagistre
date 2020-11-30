<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Transformer\DivisoredNumberToLocalizedStringTransformer;
use Money\Currency;
use Money\Money;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyType extends \Symfony\Component\Form\Extension\Core\Type\MoneyType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $divisor = $options['divisor'];

        $builder
            ->addViewTransformer(new DivisoredNumberToLocalizedStringTransformer(
                $options['scale'],
                $options['grouping'],
                null,
                $divisor
            ))
            ->addModelTransformer(new CallbackTransformer(
                fn (?Money $money) => $money instanceof Money ? $money->getAmount() : $money,
                fn (string $amount) => new Money($amount, new Currency('RUB'))
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Стоимость',
            'scale' => 2,
            'grouping' => false,
            'divisor' => 100,
            'compound' => false,
            'currency' => 'RUB',
            'html5' => false,
        ]);

        $resolver->setAllowedTypes('scale', 'int');
    }
}
