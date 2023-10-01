<?php

declare(strict_types=1);

namespace App\Review\Form;

use App\Review\Enum\ReviewSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReviewSourceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => ReviewSource::all(),
            'choice_label' => fn(ReviewSource $source) => $source->toDisplayName(),
            'choice_value' => fn(?ReviewSource $source) => null === $source ? null : $source->toId(),
            'placeholder' => null,
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
