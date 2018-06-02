<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/service/{brand}", requirements={"brand": "nissan|toyota|infinity|lexus"})
 */
final class ServiceController extends Controller
{
    /**
     * @Route("/", name="service")
     */
    public function service(): Response
    {
        return $this->render('www/service.html.twig', [
        ]);
    }

    /**
     * @Route("/repair", name="repair")
     */
    public function repair(): Response
    {
        return $this->render('www/repair.html.twig', [
        ]);
    }

    /**
     * @Route("/diagnostics/{type}", name="diagnostics", requirements={"type": "free|comp"})
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
     * @Route("/tire", name="type")
     */
    public function tire(): Response
    {
        return $this->render('www/tire_service.html.twig');
    }

    /**
     * @Route("/brands", name="brands")
     */
    public function brands(): Response
    {
        return $this->render('www/brands.html.twig');
    }

    /**
     * @Route("/corporates", name="corporates")
     */
    public function corporates()
    {
        return $this->render('www/corporates.html.twig');
    }

    /**
     * @Route("/price-list", name="price-list")
     */
    public function priceList(): Response
    {
        return $this->render('www/price_list.html.twig');
    }

    /**
     * @Route("/maintenance", name="maintenance")
     */
    public function maintenance(): Response
    {
        return $this->render('www/maintenance.html.twig');
    }

    /**
     * @Route("/contacts", name="contacts")
     */
    public function contacts(): Response
    {
        return $this->render('www/contacts.html.twig');
    }

    /**
     * @Route("/reviews", name="reviews")
     */
    public function reviews(): Response
    {
        return $this->render('www/reviews.html.twig');
    }

    /**
     * @Route("/privacy-policy", name="privacy-policy")
     */
    public function privacyPolicy(): Response
    {
        return $this->render('www/privacy_policy.html.twig');
    }
}
