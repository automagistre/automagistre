<?php

declare(strict_types=1);

namespace App\Review\Form;

use App\Review\Enum\ReviewRating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReviewRatingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => ReviewRating::all(),
            'choice_label' => fn(ReviewRating $rating) => $rating->toId(),
            'choice_value' => fn(?ReviewRating $rating) => null === $rating ? null : $rating->toId(),
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
