<?php

namespace App\Vehicle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class VinValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        \assert($constraint instanceof Vin);

        try {
            new \Sunrise\Vin\Vin($value);
        } catch (\InvalidArgumentException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ vin }}', $value)
                ->addViolation();
        }
    }
}
