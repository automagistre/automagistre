<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/test")
 */
final class TestController extends Controller
{
    /**
     * @Route
     */
    public function indexAction(): Response
    {
        return $this->render('test.html.twig');
    }
}
