<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Roles;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader(function () {
                    $roles = (new ReflectionClass(Roles::class))->getConstants();

                    unset($roles['SUPER_ADMIN'], $roles['ADMIN']);

                    $values = \array_values($roles);

                    return \array_combine($values, $values);
                }),
                'choice_translation_domain' => 'role',
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
