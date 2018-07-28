<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Employee;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WorkerType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $em = $this->em;

        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите исполнителя',
            'choice_loader' => new CallbackChoiceLoader(function () use ($em) {
                return $em->createQueryBuilder()
                    ->select('person')
                    ->from(Person::class, 'person')
                    ->join(Employee::class, 'employee', Join::WITH, 'person = employee.person')
                    ->where('employee.firedAt IS NULL')
                    ->getQuery()
                    ->getResult();
            }),
            'choice_label' => function (Person $person) {
                return (string) $person;
            },
            'choice_value' => function (?Person $person) {
                return $person instanceof Person ? $person->getId() : null;
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
}
