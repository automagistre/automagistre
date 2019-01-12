<?php

declare(strict_types=1);

namespace App\RoadRunner\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ErrorController extends AbstractController
{
    /**
     * @Route("/_rr_error")
     */
    public function __invoke(Request $request): Response
    {
        $referer = \trim($request->query->get('referer', ''));

        return $this->render('admin/error.html.twig', [
            'referer' => '' === $referer ? '/' : $referer,
        ], new Response('', Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
