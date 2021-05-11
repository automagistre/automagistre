<?php

declare(strict_types=1);

namespace App\Employee\Form;

use App\Customer\Entity\Operand;
use App\Employee\Entity\EmployeeId;
use App\Shared\Doctrine\Registry;
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
                $ids = $this->registry
                    ->connection(Operand::class)
                    ->fetchAllAssociative('SELECT id AS id FROM employee WHERE fired_at IS NULL')
                ;

                return array_map(fn (array $row) => EmployeeId::from($row['id']), $ids);
            }),
            'choice_label' => fn (EmployeeId $id) => $this->formatter->format($id),
            'choice_value' => fn (?EmployeeId $id) => null === $id ? null : $id->toString(),
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
