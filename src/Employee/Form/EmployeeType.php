<?php

declare(strict_types=1);

namespace App\Employee\Form;

use App\Employee\Entity\Employee;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function usort;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeType extends AbstractType
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
            'class' => Employee::class,
            'choice_loader' => new CallbackChoiceLoader(
                fn (): array => $this->registry->manager(Employee::class)
                    ->createQueryBuilder()
                    ->select('t')
                    ->from(Employee::class, 't')
                    ->where('t.firedAt IS NULL')
                    ->getQuery()
                    ->getResult(),
            ),
            'choice_label' => fn (Employee $employee) => $this->formatter->format($employee->toPersonId()),
            'choice_value' => fn (?Employee $employee) => null === $employee ? null : $employee->toId()->toString(),
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
