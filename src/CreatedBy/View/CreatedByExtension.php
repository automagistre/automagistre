<?php

declare(strict_types=1);

namespace App\CreatedBy\View;

use App\CreatedBy\Entity\CreatedBy;
use App\CreatedBy\Entity\CreatedByView;
use App\Shared\Doctrine\Registry;
use App\User\Entity\User;
use App\User\Entity\UserView;
use Doctrine\ORM\AbstractQuery;
use Ramsey\Uuid\UuidInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Для легаси кода, в новом коде поля должны приходить вместе с вьюхами.
 */
final class CreatedByExtension extends AbstractExtension
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'created_by_view',
                function (Environment $twig, UuidInterface $uuid): string {
                    $view = $this->registry->repository(CreatedBy::class)
                        ->createQueryBuilder('t')
                        ->where('t.id = :id')
                        ->setParameter('id', $uuid)
                        ->getQuery()
                        ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

                    $user = $this->registry->repository(User::class)
                        ->createQueryBuilder('t')
                        ->where('t.uuid = :id')
                        ->setParameter('id', $view['userId'])
                        ->getQuery()
                        ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

                    return $twig->render('easy_admin/created_by/created_by_view.html.twig', [
                        'value' => new CreatedByView(
                            new UserView(
                                $user['uuid'],
                                $user['username'],
                                $user['lastName'],
                                $user['firstName'],
                            ),
                            $view['createdAt']
                        ),
                    ]);
                },
                [
                    'needs_environment' => true,
                    'is_safe' => ['html' => true],
                ]
            ),
        ];
    }
}
