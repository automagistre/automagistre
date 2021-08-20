<?php

declare(strict_types=1);

namespace App\Customer\Validator;

use App\Customer\Entity\CustomerView;
use App\Doctrine\Registry;
use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CustomerPhoneNotExistsValidator extends ConstraintValidator
{
    public function __construct(private Registry $registry)
    {
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

        $entity = $this->registry->manager()
            ->createQueryBuilder()
            ->select('1')
            ->from(CustomerView::class, 'customer')
            ->where('customer.telephone = :telephone')
            ->setParameter('telephone', $value, 'phone_number')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null === $entity) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
