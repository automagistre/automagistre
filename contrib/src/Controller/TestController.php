<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/test")
 */
final class TestController extends AbstractController
{
    /**
     * @Route
     */
    public function indexAction(): Response
    {
        return $this->render('test.html.twig');
    }
}
