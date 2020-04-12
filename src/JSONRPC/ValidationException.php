<?php

declare(strict_types=1);

namespace App\JSONRPC;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * @psalm-immutable
 */
final class ValidationException extends BadRequestHttpException
{
    /**
     * @var ConstraintViolationListInterface<ConstraintViolationInterface>
     */
    public ConstraintViolationListInterface $violations;

    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violations
     * @param mixed                                                          $message
     * @param mixed                                                          $code
     */
    public function __construct(
        ConstraintViolationListInterface $violations,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $previous, $code);

        $this->violations = clone $violations;
    }
}
