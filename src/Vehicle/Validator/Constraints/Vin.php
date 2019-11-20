<?php

namespace App\Vehicle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Vin extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The VIN "{{ vin }}" is not compatible with ISO 3779.';
}
