<?php

namespace App;

use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class Kernel extends SymfonyKernel
{
    public function registerBundles()
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
            new \App\AppBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function boot()
    {
        parent::boot();

        $uuidFactory = new UuidFactory();
        $uuidBuilder = new DefaultUuidBuilder($uuidFactory->getNumberConverter());
        $uuidFactory->setUuidBuilder($uuidBuilder);
        $uuidFactory->setCodec(new OrderedTimeCodec($uuidBuilder));

        Uuid::setFactory($uuidFactory);
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $confDir = dirname(__DIR__).'/etc';

        $loader->load($confDir.'/container_'.$this->getEnvironment().'.yaml');
    }
}
