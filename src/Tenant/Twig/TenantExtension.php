<?php

declare(strict_types=1);

namespace App\Tenant\Twig;

use App\Tenant\EntityChecker;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantExtension extends AbstractExtension
{
    /**
     * @var EntityChecker
     */
    private $entityChecker;

    public function __construct(EntityChecker $entityChecker)
    {
        $this->entityChecker = $entityChecker;
    }

    public function getFunctions(): array
    {
        return [
            /**
             * @param object|string $entity
             */
            new TwigFunction('is_tenant_class', function (string $entity): bool {
                return $this->entityChecker->isTenantEntity($entity);
            }),
        ];
    }
}
