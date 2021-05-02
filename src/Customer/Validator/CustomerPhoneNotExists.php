<?php

declare(strict_types=1);

namespace App\Customer\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class CustomerPhoneNotExists extends Constraint
{
    /** @var string */
    public $message = 'Заказчик с таким телефоном уже существует';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
