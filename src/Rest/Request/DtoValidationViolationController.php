<?php

declare(strict_types=1);

namespace App\Rest\Request;

use function array_map;
use function iterator_to_array;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class DtoValidationViolationController extends AbstractController
{
    public function __invoke(ConstraintViolationListInterface $violations): Response
    {
        return new JsonResponse(
            array_map(static fn (ConstraintViolationInterface $violation) => [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ], iterator_to_array($violations)),
            Response::HTTP_BAD_REQUEST,
        );
    }
}
