<?php

declare(strict_types=1);

namespace App\Income\Form;

use App\Customer\Domain\Operand;
use App\Customer\Domain\OperandId;
use App\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use function array_combine;
use function array_map;
use function assert;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            fn (?OperandId $operandId) => null === $operandId ? null : $operandId->toString(),
            fn (?string $uuid) => null === $uuid ? null : OperandId::fromString($uuid)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите поставщика',
            'choice_loader' => new CallbackChoiceLoader(function () {
                $stmt = $this->registry
                    ->connection(Operand::class)
                    ->createQueryBuilder()
                    ->select('t.uuid')
                    ->from('operand', 't')
                    ->where('t.seller IS TRUE')
                    ->execute();
                assert($stmt instanceof Statement);

                $ids = array_map('array_shift', $stmt->fetchAll());

                return array_combine($ids, $ids);
            }),
            'choice_label' => fn (string $operand) => $this->formatter->format(OperandId::fromString($operand)),
            'choice_value' => fn (?string $operand) => $operand,
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
