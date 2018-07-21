<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SwitchController extends AbstractController
{
    /**
     * @Route("/", name="switch")
     */
    public function __invoke(Request $request): Response
    {
        if ('' !== $brand = $request->attributes->get('brand', '')) {
            return $this->redirectToRoute('www_service', ['brand' => $brand]);
        }

        return $this->render('www/switch.html.twig');
    }
}
