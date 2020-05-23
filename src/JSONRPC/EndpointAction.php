<?php

declare(strict_types=1);

namespace App\JSONRPC;

use Datto\JsonRpc\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EndpointAction
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function __invoke(Request $request): Response
    {
        $data = $this->server->reply($request->getContent());

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
