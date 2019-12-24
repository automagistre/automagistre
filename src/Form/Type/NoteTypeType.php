<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Enum\NoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NoteTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => NoteType::all(),
            'choice_label' => fn (NoteType $noteType) => $noteType->toName(),
            'choice_id' => fn (NoteType $noteType) => $noteType->toId(),
            'expanded' => true,
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
