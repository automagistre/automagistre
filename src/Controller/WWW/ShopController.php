<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shop")
 */
final class ShopController extends AbstractController
{
    /**
     * @Route(name="shop_index")
     */
    public function index(): Response
    {
        return $this->render('www/shop/index.html.twig');
    }
}
