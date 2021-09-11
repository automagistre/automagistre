<?php

declare(strict_types=1);

namespace App\Identifier;

use App\Customer\Entity\CustomerView;
use App\Customer\Entity\OperandId;
use App\Doctrine\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Exception\UndefinedEntityException;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use Premier\Identifier\Identifier;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IdentifierRouterExtension extends AbstractExtension
{
    public function __construct(
        private EasyAdminRouter $router,
        private IdentifierMap $identifierMap,
        private Registry $registry,
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
            ),
        ];
    }
}
