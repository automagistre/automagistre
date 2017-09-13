<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Enum\Carcase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class CarcaseType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => Carcase::all(),
            'choice_label' => 'name',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
