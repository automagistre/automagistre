<?php

declare(strict_types=1);

namespace App\Vehicle\Infrastructure\Form;

use App\Vehicle\Domain\FuelType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FuelTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => FuelType::all(),
            'choice_label' => fn (FuelType $fuelType) => $fuelType->toName(),
            'choice_value' => fn (FuelType $fuelType) => $fuelType->toId(),
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
