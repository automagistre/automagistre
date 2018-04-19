<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/blog")
 */
final class BlogController extends Controller
{
    /**
     * @Route(name="www_blog_index")
     */
    public function index(): Response
    {
        return $this->render('www/blog/index.html.twig');
    }

    /**
     * @Route("/{id}", name="www_blog_show")
     */
    public function show(): Response
    {
        return $this->render('www/blog/show.html.twig');
    }
}
