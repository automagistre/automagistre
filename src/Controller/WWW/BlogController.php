<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BlogController extends AbstractController
{
    /**
     * @Route(name="index")
     */
    public function index(): Response
    {
        return $this->render('www/blog/index.html.twig', [
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(): Response
    {
        return $this->render('www/blog/show.html.twig', [
        ]);
    }
}
