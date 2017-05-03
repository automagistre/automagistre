<?php

declare(strict_types=1);

namespace App;

use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class Kernel extends SymfonyKernel
{
    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    use MicroKernelTrait;

    public function registerBundles(): array
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle(),
            new \Misd\PhoneNumberBundle\MisdPhoneNumberBundle(),
            new \Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new \Csa\Bundle\GuzzleBundle\CsaGuzzleBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        if ($this->isSentry()) {
            $bundles[] = new \Sentry\SentryBundle\SentryBundle();
        }

        return $bundles;
    }

    public function boot(): void
    {
        parent::boot();

        $uuidFactory = new UuidFactory();
        $uuidBuilder = new DefaultUuidBuilder($uuidFactory->getNumberConverter());
        $uuidFactory->setUuidBuilder($uuidBuilder);
        $uuidFactory->setCodec(new OrderedTimeCodec($uuidBuilder));

        Uuid::setFactory($uuidFactory);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/logs';
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routingDir = $this->getProjectDir().'/etc/routing';

        $routes->import($routingDir.'/*'.self::CONFIG_EXTS, '/', 'glob');

        if (is_dir($routingDir.'/'.$this->getEnvironment())) {
            $routes->import($routingDir.'/'.$this->getEnvironment().'/*'.self::CONFIG_EXTS, '/', 'glob');
        }
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir().'/etc';

        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->getEnvironment())) {
            $loader->load($confDir.'/packages/'.$this->getEnvironment().'/*'.self::CONFIG_EXTS, 'glob');
        }

        if ($this->isSentry()) {
            $loader->load($confDir.'/packages/lazy/sentry.yaml');
        }

        $loader->load($confDir.'/container'.self::CONFIG_EXTS, 'glob');
    }

    private function isSentry(): bool
    {
        return (bool) getenv('SENTRY_DSN');
    }
}
