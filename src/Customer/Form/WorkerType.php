<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Order\Entity\OrderItemService;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use function array_flip;
use function array_key_exists;
use function array_map;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WorkerType extends AbstractType
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
        $preferred = $this->getPreferredOperands();

        $groupMap = [];

        $resolver->setDefaults([
            'label' => 'Исполнитель',
            'placeholder' => 'Выберите исполнителя',
            'class' => Operand::class,
            'choice_loader' => new CallbackChoiceLoader(function () use (&$groupMap): array {
                $ids = $this->registry
                    ->connection(Operand::class)
                    ->fetchAll('SELECT uuid AS id, type FROM operand WHERE contractor IS TRUE');

                foreach ($ids as ['id' => $id, 'type' => $type]) {
                    $groupMap[$id] = $type;
                }

                return array_map(fn (array $item): OperandId => OperandId::fromString($item['id']), $ids);
            }),
            'preferred_choices' => fn (string $operand) => array_key_exists($operand, $preferred),
            'choice_label' => fn (OperandId $operandId) => $this->formatter->format($operandId),
            'choice_value' => fn (?OperandId $operandId) => null === $operandId ? null : $operandId->toString(),
            'group_by' => static function (string $operand) use (&$groupMap) {
                return [
                    '1' => 'Работник',
                    '2' => 'Организация',
                ][$groupMap[$operand]];
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getPreferredOperands(): array
    {
        $em = $this->registry->manager(OrderItemService::class);

        $services = $em->createQueryBuilder()
            ->select('entity.workerId')
            ->from(OrderItemService::class, 'entity')
            ->where('entity.createdAt > :today')
            ->andWhere('entity.workerId IS NOT NULL')
            ->groupBy('entity.workerId')
            ->setParameter('today', new DateTime('-1000 hour'))
            ->getQuery()
            ->getScalarResult();

        $services = array_map('array_shift', $services);

        return array_flip($services);
    }
}
