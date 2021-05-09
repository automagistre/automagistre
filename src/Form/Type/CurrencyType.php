<?php

declare(strict_types=1);

namespace App\Form\Type;

use Money\Currencies;
use Money\Currency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function in_array;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CurrencyType extends AbstractType
{
    private const PREFERRED = ['RUB', 'USD', 'EUR'];

    public function __construct(private Currencies $currencies)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => $this->currencies->getIterator(),
                'choice_label' => 'code',
                'choice_value' => 'code',
                'preferred_choices' => fn (Currency $currency) => in_array($currency->getCode(), self::PREFERRED, true),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
