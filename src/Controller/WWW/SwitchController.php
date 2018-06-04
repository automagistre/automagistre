<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SwitchController extends Controller
{
    /**
     * @Route("/", name="switch")
     */
    public function __invoke(Request $request): Response
    {
        if ($brand = $request->attributes->get('brand')) {
            return $this->redirectToRoute('www_service', ['brand' => $brand]);
        }

        return $this->render('www/switch.html.twig');
    }
}
