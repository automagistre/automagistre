<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends SymfonyKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        $contents = require $this->getConfDir().'/bundles.php';
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

    public function getRootDir(): string
    {
        return $this->getProjectDir().'/public';
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getConfDir();

        if (is_dir($confDir.'/routing/'.$this->getEnvironment())) {
            $routes->import($confDir.'/routing/'.$this->getEnvironment().'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($confDir.'/routing'.self::CONFIG_EXTS, '/', 'glob');
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $confDir = $this->getConfDir();

        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');

        if ('test' === $this->getEnvironment() && is_dir($confDir.'/packages/dev')) {
            $loader->load($confDir.'/packages/dev/*'.self::CONFIG_EXTS, 'glob');
        }

        if (is_dir($confDir.'/packages/'.$this->getEnvironment())) {
            $loader->load($confDir.'/packages/'.$this->getEnvironment().'/*'.self::CONFIG_EXTS, 'glob');
        }

        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/conflicts'.self::CONFIG_EXTS, 'glob');
    }
}
