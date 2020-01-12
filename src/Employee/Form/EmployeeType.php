<?php

declare(strict_types=1);

namespace App\Employee\Form;

use App\Entity\Tenant\Employee;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function usort;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите работника',
            'class' => Employee::class,
            'query_builder' => fn (EntityRepository $repository) => $repository->createQueryBuilder('entity')
                ->where('entity.firedAt IS NULL'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @psalm-suppress MissingPropertyType */
        usort($view->vars['choices'], fn (ChoiceView $left, ChoiceView $right): int => $left->label <=> $right->label);

        parent::finishView($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }
}
