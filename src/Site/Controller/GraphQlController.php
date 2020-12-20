<?php

declare(strict_types=1);

namespace App\Site\Controller;

use App\Shared\Doctrine\Registry;
use App\Site\Context;
use App\Site\Schema;
use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GraphQlController extends AbstractController
{
    private Registry $registry;

    private bool $debug;

    public function __construct(Registry $registry, bool $debug)
    {
        $this->registry = $registry;
        $this->debug = $debug;
    }

    /**
     * @Route("/api/www")
     */
    public function www(Request $request): Response
    {
        $query = $request->query->get('query');
        $variableValues = null;

        if ($request->isMethod('POST')) {
            $payload = $request->toArray();

            $query = $payload['query'] ?? '';
            $variableValues = $payload['variables'] ?? [];
        }

        $schema = Schema::create();
        $context = new Context($this->registry);

        $result = GraphQL::executeQuery($schema, $query ?? '', null, $context, $variableValues);

        $debugFlag = $this->debug ? DebugFlag::RETHROW_UNSAFE_EXCEPTIONS : DebugFlag::NONE;

        return new JsonResponse($result->toArray($debugFlag));
    }
}
