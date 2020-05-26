<?php

declare(strict_types=1);

namespace App\Customer\Validator;

use App\Customer\Entity\Operand;
use App\Customer\Entity\Organization;
use App\Customer\Entity\Person;
use App\Shared\Doctrine\Registry;
use Doctrine\ORM\Query\Expr\Join;
use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CustomerPhoneNotExistsValidator extends ConstraintValidator
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CustomerPhoneNotExists) {
            throw new UnexpectedTypeException($constraint, CustomerPhoneNotExists::class);
        }

        if (!$value instanceof PhoneNumber) {
            return;
        }

        $entity = $this->registry->manager(Operand::class)
            ->createQueryBuilder()
            ->select('1')
            ->from(Operand::class, 'operand')
            ->leftJoin(Person::class, 'person', Join::WITH, 'operand.id = person.id')
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'operand.id = organization.id')
            ->where('person.telephone = :telephone')
            ->orWhere('organization.telephone = :telephone')
            ->setParameter('telephone', $value, 'phone_number')
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $entity) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
