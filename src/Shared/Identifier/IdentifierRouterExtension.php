<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use function get_class;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IdentifierRouterExtension extends AbstractExtension
{
    private EasyAdminRouter $router;

    private IdentifierMap $identifierMap;

    public function __construct(EasyAdminRouter $router, IdentifierMap $identifierMap)
    {
        $this->router = $router;
        $this->identifierMap = $identifierMap;
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
                    $class = $this->identifierMap->entityClassByIdentifier(get_class($uuid));
                    $params['id'] = $uuid->toString();

                    return $this->router->generate($class, $action, $params);
                }
            ),
        ];
    }
}
