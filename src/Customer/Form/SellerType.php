<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Entity\CustomerView;
use App\Customer\Entity\OperandId;
use App\Doctrine\Registry;
use App\Income\Entity\Income;
use App\Shared\Identifier\IdentifierFormatter;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_key_exists;
use function array_map;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SellerType extends AbstractType
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
        $preferred = $this->registry->manager()->createQueryBuilder()
            ->select('t.supplierId')
            ->from(Income::class, 't', 't.supplierId')
            ->where('t.accruedAt > :date')
            ->groupBy('t.supplierId')
            ->orderBy('COUNT(t.supplierId)', 'DESC')
            ->getQuery()
            ->setParameter('date', new DateTime('-1 month'))
            ->setMaxResults(10)
            ->getArrayResult()
        ;

        $resolver->setDefaults([
            'label' => 'Поставщик',
            'placeholder' => 'Выберите поставщика',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                $sellers = $this->registry->findBy(CustomerView::class, ['seller' => true]);

                return array_map(static fn (CustomerView $seller): OperandId => $seller->id, $sellers);
            }),
            'choice_label' => fn (OperandId $operandId) => $this->formatter->format($operandId),
            'choice_value' => fn (?OperandId $operandId) => $operandId?->toString(),
            'preferred_choices' => fn (string $operand) => array_key_exists($operand, $preferred),
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
