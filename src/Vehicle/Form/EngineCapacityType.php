<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use function array_combine;
use function array_map;
use function number_format;
use function range;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EngineCapacityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => new CallbackChoiceLoader(static function (): array {
                $choices = array_map(
                    static fn (float $number) => number_format($number, 1),
                    range(0.6, 6.0, 0.1),
                );

                /** @var array $choices */
                $choices = array_combine($choices, $choices);

                return $choices;
            }),
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
