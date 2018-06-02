<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use App\Router\BrandListener;
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
        if ($request->hasSession()) {
            $session = $request->getSession();

            if ($session->has(BrandListener::BRAND_SESSION_ATTRIBUTE)) {
                $brand = $session->get(BrandListener::BRAND_SESSION_ATTRIBUTE);

                return $this->redirectToRoute('service', ['brand' => $brand]);
            }
        }

        return $this->render('www/switch.html.twig');
    }
}
