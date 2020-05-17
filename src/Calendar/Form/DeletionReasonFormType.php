<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Calendar\Enum\DeletionReason;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeletionReasonFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => DeletionReason::all(),
            'choice_label' => fn (?DeletionReason $enum) => null !== $enum ? $enum->toName() : null,
            'choice_value' => fn (?DeletionReason $enum) => null !== $enum ? $enum->toId() : null,
            'placeholder' => 'Выберите причину',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
