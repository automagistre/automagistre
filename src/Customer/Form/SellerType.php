<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use function array_map;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите поставщика',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                $sellers = $this->registry->manager(Operand::class)
                    ->createQueryBuilder()
                    ->select('t.uuid AS id')
                    ->from(Operand::class, 't')
                    ->where('t.seller = TRUE')
                    ->getQuery()
                    ->getResult(AbstractQuery::HYDRATE_ARRAY);

                return array_map('array_shift', $sellers);
            }),
            'choice_label' => fn (OperandId $operandId) => $this->formatter->format($operandId),
            'choice_value' => fn (?OperandId $operandId) => null === $operandId ? null : $operandId->toString(),
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
