<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\Tenant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class HomeController extends AbstractController
{
    /**
     * @Route
     */
    public function __invoke(): Response
    {
        return $this->redirectToRoute('easyadmin', [
            'tenant' => Tenant::msk()->getIdentifier(),
        ]);
    }
}
