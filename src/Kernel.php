<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Identifier\IdentifierFormatter;
use App\JSONRPC\Test\JsonRPCClient;
use App\Tenant\MetadataCompilerPass;
use function assert;
use function class_exists;
use function dirname;
use function file_exists;
use InvalidArgumentException;
use function is_dir;
use function is_subclass_of;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends SymfonyKernel implements CompilerPassInterface
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
    public function process(ContainerBuilder $container): void
    {
        if ('test' === $this->environment) {
            $container->getDefinition('test.client')->setClass(JsonRPCClient::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MetadataCompilerPass(), PassConfig::TYPE_OPTIMIZE);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getConfDir();

        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($confDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');

        $projectDir = $this->getProjectDir();
        $routes->import($projectDir.'/src/*/routes'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($projectDir.'/src/*/routes_'.$this->environment.self::CONFIG_EXTS, '/', 'glob');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->setParameter('container.autowiring.strict_mode', true);

        $env = $this->getEnvironment();
        $confDir = $this->getConfDir();
        $projectDir = $this->getProjectDir();

        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');

        if (is_dir($confDir.'/packages/'.$env)) {
            $loader->load($confDir.'/packages/'.$env.'/**/*'.self::CONFIG_EXTS, 'glob');
        }

        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services_'.$env.self::CONFIG_EXTS, 'glob');

        $loader->load($projectDir.'/src/*/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($projectDir.'/src/*/services_'.$env.self::CONFIG_EXTS, 'glob');

        $loader->load($projectDir.'/src/*/config'.self::CONFIG_EXTS, 'glob');
        $loader->load($projectDir.'/src/*/config_'.$env.self::CONFIG_EXTS, 'glob');
    }
}
