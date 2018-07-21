<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
final class BlogController extends AbstractController
{
    /**
     * @Route(name="blog_index")
     */
    public function index(): Response
    {
        return $this->render('www/blog/index.html.twig');
    }

    /**
     * @Route("/{id}", name="blog_show")
     */
    public function show(): Response
    {
        return $this->render('www/blog/show.html.twig');
    }
}
