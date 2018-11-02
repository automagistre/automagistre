<?php

declare(strict_types=1);

namespace App\Controller\WWW\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ContactsController extends AbstractController
{
    /**
     * @Route("/contacts", name="contacts")
     */
    public function __invoke(): Response
    {
        return $this->render('www/contacts.html.twig', [
        ]);
    }
}
