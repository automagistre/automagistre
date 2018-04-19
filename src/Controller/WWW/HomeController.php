<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class HomeController extends Controller
{
    /**
     * @Route("/switch", name="www_switch")
     */
    public function __invoke(): Response
    {
        return $this->render('www/switch.html.twig', [
        ]);
    }

    /**
     * @Route("/", name="www_homepage")
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

    /**
     * @Route("/diagnostics/{type}", name="www_diagnostics", requirements={"type": "free|comp"})
     */
    public function diagnostics(string $type): Response
    {
        if ('comp' === $type) {
            return $this->render('www/diagnostics_comp.html.twig');
        }

        if ('free' === $type) {
            return $this->render('www/diagnostics_free.html.twig');
        }

        throw new LogicException('Unreachable statement');
    }

    /**
     * @Route("/tire", name="www_type")
     */
    public function tire(): Response
    {
        return $this->render('www/tire_service.html.twig');
    }

    /**
     * @Route("/brands", name="www_brands")
     */
    public function brands(): Response
    {
        return $this->render('www/brands.html.twig');
    }

    /**
     * @Route("/corporates", name="www_corporates")
     */
    public function corporates()
    {
        return $this->render('www/corporates.html.twig');
    }

    /**
     * @Route("/price-list", name="www_price-list")
     */
    public function priceList(): Response
    {
        return $this->render('www/price_list.html.twig');
    }

    /**
     * @Route("/maintenance", name="www_maintenance")
     */
    public function maintenance(): Response
    {
        return $this->render('www/maintenance.html.twig');
    }

    /**
     * @Route("/contacts", name="www_contacts")
     */
    public function contacts(): Response
    {
        return $this->render('www/contacts.html.twig');
    }

    /**
     * @Route("/reviews", name="www_reviews")
     */
    public function reviews(): Response
    {
        return $this->render('www/reviews.html.twig');
    }
}
