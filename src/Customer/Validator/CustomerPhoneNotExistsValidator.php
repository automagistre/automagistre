<?php

declare(strict_types=1);

namespace App\Customer\Validator;

use App\Customer\Entity\CustomerView;
use App\Doctrine\Registry;
use libphonenumber\PhoneNumber;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CustomerPhoneNotExistsValidator extends ConstraintValidator
{
    public function __construct(private Registry $registry, private RequestStack $requestStack)
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

        $entity = $this->registry->findOneBy(CustomerView::class, ['telephone' => $value]);

        if (null === $entity) {
            return;
        }

        $request = $this->requestStack->getMainRequest();

        if (null !== $request && $entity->toId()->toString() === $request->query->get('id')) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
