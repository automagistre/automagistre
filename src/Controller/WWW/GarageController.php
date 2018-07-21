<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/garage")
 */
final class GarageController extends AbstractController
{
    /**
     * @Route(name="garage_index")
     */
    public function index(): Response
    {
        return $this->render('www/garage/index.html.twig');
    }
}
