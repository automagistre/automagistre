<?php

declare(strict_types=1);

namespace App\Review\Yandex\Controller;

use App\Tenant\State;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RedirectController
{
    /**
     * @Route("/ymap", name="yandex_map_url")
     */
    public function __invoke(State $state): RedirectResponse
    {
        return new RedirectResponse($state->get()->toYandexMapUrl(), Response::HTTP_SEE_OTHER);
    }
}
