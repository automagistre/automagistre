<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_flip;
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
        $preferred = $this->registry->connection()
            ->fetchAllAssociative(
                '
                SELECT supplier_id
                FROM income
                WHERE accrued_at > :date
                GROUP BY supplier_id
                ORDER BY COUNT(*) DESC
                LIMIT 10
            ',
                [
                    'date' => (new DateTime('-1 month'))->format('Y-m-d'),
                ]
            )
        ;

        $preferred = array_map(static fn (array $item) => array_shift($item), $preferred);
        $preferred = array_flip($preferred);

        $resolver->setDefaults([
            'label' => 'Поставщик',
            'placeholder' => 'Выберите поставщика',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                $ids = $this->registry
                    ->connection(Operand::class)
                    ->fetchAllAssociative('SELECT id AS id, type FROM operand WHERE seller IS TRUE')
                ;

                return array_map(fn (array $item): OperandId => OperandId::fromString($item['id']), $ids);
            }),
            'choice_label' => fn (OperandId $operandId) => $this->formatter->format($operandId),
            'choice_value' => fn (?OperandId $operandId) => null === $operandId ? null : $operandId->toString(),
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
