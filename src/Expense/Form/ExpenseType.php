<?php

declare(strict_types=1);

namespace App\Expense\Form;

use App\Doctrine\Registry;
use App\Expense\Entity\Expense;
use App\Expense\Entity\ExpenseId;
use App\Identifier\IdentifierFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_map;

final class ExpenseType extends AbstractType
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
            'label' => 'Статья расходов',
            'placeholder' => 'Выберите статью расходов',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                return array_map(
                    static fn (Expense $expense): ExpenseId => $expense->toId(),
                    $this->registry->findBy(Expense::class),
                );
            }),
            'choice_label' => fn (ExpenseId $expenseId) => $this->formatter->format($expenseId),
            'choice_value' => fn (?ExpenseId $expenseId) => null === $expenseId ? null : $expenseId->toString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
