<?php

declare(strict_types=1);

namespace App\Shared\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function assert;
use function is_object;
use function method_exists;

final class EntityCheckValidator extends ConstraintValidator
{
    public function __construct(private ManagerRegistry $registry, private PropertyAccessorInterface $accessor)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityCheck) {
            throw new UnexpectedTypeException($constraint, EntityCheck::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, 'object');
        }

        $em = $this->registry->getManagerForClass($constraint->class);
        assert($em instanceof EntityManagerInterface);

        $qb = $em
            ->createQueryBuilder()
            ->select('1')
            ->from($constraint->class, 'entity')
        ;

        foreach ($constraint->fields as $property => $field) {
            $fieldValue = $this->accessor->getValue($value, $property);

            if (is_object($fieldValue) && method_exists($fieldValue, 'toId')) {
                $fieldValue = $fieldValue->toId()->toString();
            }

            $qb
                ->andWhere("entity.{$field} = :{$property}")
                ->setParameter($property, $fieldValue)
            ;
        }

        $entity = $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (
            (null === $entity && $constraint->exists)
            || (null !== $entity && !$constraint->exists)
        ) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->errorPath)
                ->addViolation()
            ;
        }
    }
}
