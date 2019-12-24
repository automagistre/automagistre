<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Enum\Carcase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarcaseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => Carcase::all(),
            'choice_label' => fn (Carcase $carcase) => $carcase->toName(),
            'choice_value' => fn (Carcase $carcase) => $carcase->toId(),
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
