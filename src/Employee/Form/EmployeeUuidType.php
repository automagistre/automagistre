<?php

declare(strict_types=1);

namespace App\Employee\Form;

use App\Doctrine\Registry;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Shared\Identifier\IdentifierFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_map;
use function usort;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeUuidType extends AbstractType
{
    private Registry $registry;

    private IdentifierFormatter $formatter;

    public function __construct(Registry $registry, IdentifierFormatter $formatter)
    {
        $this->registry = $registry;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите работника',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                $employees = $this->registry->findBy(Employee::class, ['firedAt' => null]);

                return array_map(static fn (Employee $employee) => $employee->toId(), $employees);
            }),
            'choice_label' => fn (EmployeeId $id) => $this->formatter->format($id),
            'choice_value' => fn (?EmployeeId $id) => $id?->toString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        usort(
            $view->vars['choices'],
            static fn (ChoiceView $left, ChoiceView $right): int => $left->label <=> $right->label,
        );

        parent::finishView($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
