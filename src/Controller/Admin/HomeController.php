<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Landlord\User;
use App\Tenant\Tenant;
use function assert;
use function reset;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class HomeController extends AbstractController
{
    /**
     * @Route
     */
    public function __invoke(): Response
    {
        $user = $this->getUser();
        $tenants = $user->getTenants();
        $tenant = reset($tenants);
        assert($tenant instanceof Tenant);

        return $this->redirectToRoute('easyadmin', [
            'tenant' => $tenant->getIdentifier(),
        ]);
    }
}
