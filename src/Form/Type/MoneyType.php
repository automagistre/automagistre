<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Transformer\DivisoredNumberTransformer;
use Money\Currency;
use Money\Money;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addViewTransformer(new CallbackTransformer(
                fn (?int $amount) => $amount,
                fn (?int $amount) => match (true) {
                    null === $amount => null,
                    0 > $amount => throw new TransformationFailedException(invalidMessage: 'Значение не может быть меньше нуля.'),
                    default => $amount,
                },
            ))
            ->addViewTransformer(new DivisoredNumberTransformer())
            ->addModelTransformer(new CallbackTransformer(
                fn (?Money $money) => $money instanceof Money ? $money->getAmount() : $money,
                fn (string $amount) => new Money($amount, new Currency('RUB')),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'label' => 'Стоимость',
            'compound' => false,
            'empty_data' => 0,
            'required' => true,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'money_money';
    }
}
