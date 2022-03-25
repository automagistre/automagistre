<?php

declare(strict_types=1);

namespace App\Identifier;

use App\Customer\Entity\CustomerView;
use App\Customer\Entity\OperandId;
use App\Doctrine\Registry;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Exception\UndefinedEntityException;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use Premier\Identifier\Identifier;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IdentifierRouterExtension extends AbstractExtension
{
    public function __construct(
        private EasyAdminRouter $router,
        private IdentifierMap $identifierMap,
        private Registry $registry,
        private CacheInterface $cache,
    ) {
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
                    $key = sprintf('easyadmin_path_by_id.%s.%s.%s', $uuid->toString(), $action, http_build_query($params));

                    return $this->cache->get(
                        $key,
                        function (CacheItemInterface $item) use ($uuid, $action, $params): string {
                            $item->expiresAt(new DateTimeImmutable('+1 month'));

                            $class = $this->identifierMap->entityClassByIdentifier($uuid);
                            $params['id'] = $uuid->toString();

                            if ($uuid instanceof OperandId) {
                                $class = $this->registry->get(CustomerView::class, $uuid)->toClass();
                            }

                            try {
                                return $this->router->generate($class, $action, $params);
                            } catch (UndefinedEntityException) {
                                return $this->router->generate($class.'View', $action, $params);
                            }
                        },
                    );
                },
            ),
        ];
    }
}
