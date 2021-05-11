<?php

declare(strict_types=1);

namespace App\Shared\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @psalm-suppress MissingConstructor
 */
final class EntityCheck extends Constraint
{
    /** @var string */
    public $message;

    /** @var string */
    public $class;

    /** @var array */
    public $fields;

    /** @var bool */
    public $exists = true;

    /** @var string */
    public $errorPath;

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
