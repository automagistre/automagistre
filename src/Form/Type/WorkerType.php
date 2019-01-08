<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Landlord\Operand;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Employee;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WorkerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите исполнителя',
            'class' => Operand::class,
            'query_builder' => function (EntityRepository $repository) {
                $qb = $repository->createQueryBuilder('entity');
                $expr = $qb->expr();

                return $qb
                    ->leftJoin(Person::class, 'person', Join::WITH, 'entity.id = person.id')
                    ->leftJoin(Employee::class, 'employee', Join::WITH, 'person = employee.person')
                    ->where($expr->andX(
                        $expr->isNotNull('employee'),
                        $expr->isNull('employee.firedAt')
                    ))
                    ->orWhere('entity.contractor = :is_contractor')
                    ->setParameter('is_contractor', true);
            },
            'choice_label' => function (Operand $operand) {
                return (string) $operand;
            },
            'choice_value' => 'id',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }
}
