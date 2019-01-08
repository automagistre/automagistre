<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class HomeController extends AbstractController
{
    /**
     * @Route("/home")
     */
    public function __invoke(): Response
    {
        return new Response('yo');
    }
}
