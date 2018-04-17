<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function __invoke(): Response
    {
        return $this->render('www/index.html.twig', [
        ]);
    }

    /**
     * @Route("/home", name="www_homepage")
     */
    public function home(): Response
    {
        return $this->render('www/homepage.html.twig', [
        ]);
    }

    /**
     * @Route("/repair", name="www_repair")
     */
    public function repair(): Response
    {
        return $this->render('www/repair.html.twig', [
        ]);
    }
}
