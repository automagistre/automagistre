<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Landlord\Tenant;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class HomeController extends AbstractController
{
    /**
     * @Route
     */
    public function __invoke(RegistryInterface $registry): Response
    {
        /** @var Tenant[] $tenants */
        $tenants = $registry->getManagerForClass(Tenant::class)
            ->getRepository(Tenant::class)->findBy([], ['id' => 'ASC'], 1);

        if ([] === $tenants) {
            throw new NotFoundHttpException('You need to create at lease one Tenant in database.');
        }

        return $this->redirectToRoute('easyadmin', [
            'tenant' => $tenants[0]->identifier,
        ]);
    }
}
