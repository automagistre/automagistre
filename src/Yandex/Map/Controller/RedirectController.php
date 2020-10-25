<?php

declare(strict_types=1);

namespace App\Yandex\Map\Controller;

use App\Tenant\Tenant;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RedirectController
{
    /**
     * @Route("/ymap", name="yandex_map_url")
     */
    public function __invoke(Tenant $tenant): RedirectResponse
    {
        return new RedirectResponse($tenant->toYandexMapUrl(), Response::HTTP_SEE_OTHER);
    }
}
