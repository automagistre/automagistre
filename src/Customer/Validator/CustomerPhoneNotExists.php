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

    /** @var string */
    public $errorPath;

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
