<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends SymfonyKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        /** @noinspection PhpIncludeInspection */
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ((array) $contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->getEnvironment()])) {
                yield new $class();
            }
        }
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/logs';
    }

    public function getConfDir(): string
    {
        return $this->getProjectDir().'/config';
    }

    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('fos_user.registration.form.factory')->setPublic(true);
        $container->getAlias('fos_user.user_manager')->setPublic(true);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getConfDir();

        if (is_dir($confDir.'/routes/')) {
            $routes->import($confDir.'/routes/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($confDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->setParameter('container.autowiring.strict_mode', true);

        $confDir = $this->getConfDir();

        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');

        if (is_dir($confDir.'/packages/'.$this->getEnvironment())) {
            $loader->load($confDir.'/packages/'.$this->getEnvironment().'/**/*'.self::CONFIG_EXTS, 'glob');
        }

        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services_'.$this->getEnvironment().self::CONFIG_EXTS, 'glob');
    }
}
