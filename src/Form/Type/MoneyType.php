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
class MoneyType extends \Symfony\Component\Form\Extension\Core\Type\MoneyType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addViewTransformer(new DivisoredNumberToLocalizedStringTransformer(
                $options['scale'],
                $options['grouping'],
                null,
                $options['divisor']
            ))
            ->addModelTransformer(new CallbackTransformer(function ($money) {
                return $money instanceof Money ? $money->getAmount() : $money;
            }, function (string $amount) {
                return new Money($amount, new Currency('RUB'));
            }));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'scale' => 2,
            'grouping' => false,
            'divisor' => 100,
            'compound' => false,
            'currency' => 'RUB',
        ]);

        $resolver->setAllowedTypes('scale', 'int');
    }
}
