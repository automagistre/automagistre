<?php

declare(strict_types=1);

namespace App\Controller\WWW\Service;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TireController extends AbstractController
{
    /**
     * @Route("/tire", name="type")
     */
    public function __invoke(): Response
    {
        return $this->render('www/tire_service.html.twig', [
        ]);
    }
}
