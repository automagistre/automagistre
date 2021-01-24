<?php

declare(strict_types=1);

namespace App\Employee\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_combine;
use function range;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PayDayType extends AbstractType
{
    private const MONTH_FIRST_DAY = 1;
    private const SHORT_MONTH_END_DAY = 28;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = range(self::MONTH_FIRST_DAY, self::SHORT_MONTH_END_DAY);

        $resolver
            ->setDefaults([
                'choices' => array_combine($choices, $choices),
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
