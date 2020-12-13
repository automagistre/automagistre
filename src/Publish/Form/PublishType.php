<?php

declare(strict_types=1);

namespace App\Publish\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PublishType extends AbstractType
{
    private const PUBLISHED = '2';
    private const NOT_PUBLISHED = '1';

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress InvalidArrayOffset
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $publish = $builder->create('publish', HiddenType::class)
            ->addViewTransformer(
                new CallbackTransformer(
                    fn (bool $value): string => $value ? self::PUBLISHED : self::NOT_PUBLISHED,
                    fn (string $value): bool => [self::PUBLISHED => true, self::NOT_PUBLISHED => false][$value],
                ),
            );

        $builder
            ->add('id', HiddenType::class)
            ->add($publish);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublishDto::class,
        ]);
    }
}
