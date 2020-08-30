<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Vehicle\Enum\BodyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BodyTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => BodyType::all(),
            'choice_label' => fn (BodyType $bodyType) => $bodyType->toName(),
            'choice_value' => fn (?BodyType $bodyType) => null === $bodyType ? null : $bodyType->toId(),
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
