<?php

declare(strict_types=1);

namespace App;

use App\Shared\Doctrine\ORM\Listeners\MetadataCacheCompilerPass;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierMapCompilerPass;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function assert;
use function class_exists;
use function dirname;
use function file_exists;
use function is_subclass_of;
use function sprintf;

final class Kernel extends SymfonyKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private static IdentifierFormatter $formatter;

    /**
     * @return iterable<int, BundleInterface>
     */
    public function registerBundles(): iterable
    {
        $path = $this->getProjectDir().'/config/bundles.php';
        assert(file_exists($path));

        /** @noinspection PhpIncludeInspection */
        $contents = require $path;
        foreach ((array) $contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->getEnvironment()])) {
                if (!class_exists($class)) {
                    throw new InvalidArgumentException(sprintf('Bundle %s not found.', $class));
                }

                assert(is_subclass_of($class, BundleInterface::class));

                yield new $class();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getConfDir(): string
    {
        return $this->getProjectDir().'/config';
    }

    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        parent::boot();

        Costil::$formatter = $this->getContainer()->get(IdentifierFormatter::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MetadataCacheCompilerPass($this), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new IdentifierMapCompilerPass(), PassConfig::TYPE_OPTIMIZE);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/*'.self::CONFIG_EXTS);
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS);
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);

        $confDir = $this->getProjectDir().'/config';
        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $projectDir = $this->getProjectDir();
        $loader->load($projectDir.'/src/*/config'.self::CONFIG_EXTS, 'glob');
        $loader->load($projectDir.'/src/*/config_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }
}
