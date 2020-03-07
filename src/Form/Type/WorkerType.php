<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Landlord\Organization;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\OrderItemService;
use function array_key_exists;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function get_class;
use LogicException;
use function sprintf;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WorkerType extends AbstractType
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $preferred = $this->getPreferredOperands();

        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите исполнителя',
            'class' => Operand::class,
            'query_builder' => fn (EntityRepository $repository) => $repository->createQueryBuilder('entity')
                ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = entity.id AND entity INSTANCE OF '.Person::class)
                ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = entity.id AND entity INSTANCE OF '.Organization::class)
                ->where('entity.contractor = :is_contractor')
                ->orderBy('person.lastname', 'ASC')
                ->addOrderBy('organization.name', 'ASC')
                ->setParameter('is_contractor', true),
            'preferred_choices' => fn (Operand $operand) => array_key_exists($operand->getId(), $preferred),
            'choice_label' => fn (Operand $operand) => (string) $operand,
            'choice_value' => 'id',
            'group_by' => static function (Operand $operand) {
                if ($operand instanceof Person) {
                    return 'Работник';
                }

                if ($operand instanceof Organization) {
                    return 'Организация';
                }

                throw new LogicException(sprintf('Unexpected Operand: "%s"', get_class($operand)));
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }

    private function getPreferredOperands(): array
    {
        $em = $this->registry->manager(OrderItemService::class);

        $services = $em->createQueryBuilder()
            ->select('entity.worker.id')
            ->from(OrderItemService::class, 'entity')
            ->where('entity.createdAt > :today')
            ->andWhere('entity.worker.id IS NOT NULL')
            ->groupBy('entity.worker.id')
            ->setParameter('today', new DateTime('-10 hour'))
            ->getQuery()
            ->getScalarResult();

        $map = [];
        foreach ($services as $service) {
            $map[$service['worker.id']] = true;
        }

        return $map;
    }
}
