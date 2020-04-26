<?php

declare(strict_types=1);

namespace App\Vehicle\Infrastructure\Form;

use App\Vehicle\Domain\Transmission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TransmissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => Transmission::all(),
            'choice_label' => fn (Transmission $transmission) => $transmission->toName(),
            'choice_value' => fn (Transmission $transmission) => $transmission->toId(),
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
