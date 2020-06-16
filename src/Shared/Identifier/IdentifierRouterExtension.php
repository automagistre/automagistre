<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use App\Costil;
use App\Shared\Doctrine\Registry;
use Doctrine\ORM\NoResultException;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use function get_class;
use LogicException;
use function sprintf;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IdentifierRouterExtension extends AbstractExtension
{
    private Registry $registry;

    private EasyAdminRouter $router;

    public function __construct(Registry $registry, EasyAdminRouter $router)
    {
        $this->registry = $registry;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'easyadmin_path_by_id',
                function (Identifier $uuid, string $action, array $params = []): string {
                    $class = get_class($uuid);
                    $uuidField = Costil::UUID_FIELDS[$class] ?? null;

                    try {
                        $params['id'] = null === $uuidField
                            ? $uuid->toString()
                            : $this->registry->manager(Costil::ENTITY[$class])
                                ->createQueryBuilder()
                                ->select('t.id')
                                ->from(Costil::ENTITY[$class], 't')
                                ->where('t.'.$uuidField.' = :uuid')
                                ->setParameter('uuid', $uuid)
                                ->getQuery()
                                ->getSingleScalarResult();
                    } catch (NoResultException $e) {
                        throw new LogicException(sprintf('Not Found entity %s by id %s', $class, $uuid->toString()));
                    }

                    return $this->router->generate(Costil::EASYADMIN_CONFIG[$class], $action, $params);
                }
            ),
        ];
    }
}
