<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\DependencyInjection\Compiler;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EasyAdminConfigPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $configPasses = $this->findAndSortTaggedServices('easyadmin.config_pass', $container);
        $definition = $container->getDefinition(ConfigManager::class);

        foreach ($configPasses as $service) {
            $definition->addMethodCall('addConfigPass', [$service]);
        }
    }
}
