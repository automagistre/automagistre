<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use App\Doctrine\DBAL\Types\EnumType;
use App\Utils\StringUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EnumDoctrineTypesCompilerPass implements CompilerPassInterface
{
    public const AUTOLOAD_FILE = 'grachevkoEnumDoctrineTypesAutoload.php';

    private const GENERATED_NAMESPACE = 'App\\Doctrine\\DBAL\\Types\\Generated';

    private const DOCTRINE_DBAL_CONNECTION_FACTORY_TYPES = 'doctrine.dbal.connection_factory.types';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');
        $cacheDir = $container->getParameter('kernel.cache_dir');
        $generatedDir = $cacheDir.'/doctrine_enum_types/';

        (new Filesystem())->mkdir($generatedDir);

        $doctrineTypes = $container->getParameter(self::DOCTRINE_DBAL_CONNECTION_FACTORY_TYPES);

        $typeFiles = [];
        foreach ((new Finder())->in($projectDir.'/src/Enum') as $file) {
            /** @var SplFileInfo $file */
            $enumClass = $file->getBasename('.php');
            $enumNamespace = 'App\\Enum\\'.$enumClass;

            $type = StringUtils::underscore($enumClass);
            $typeClass = $enumClass.'Type';
            $typeFiles[] = $typeFile = $generatedDir.$typeClass.'.php';

            $class = new ClassGenerator($typeClass, self::GENERATED_NAMESPACE, null, EnumType::class);
            $class->addMethodFromGenerator(
                (new MethodGenerator('getName', [], MethodGenerator::FLAG_PUBLIC, \sprintf('return \'%s\';', $type)))
                    ->setReturnType('string')
            );
            $class->addMethodFromGenerator(
                (new MethodGenerator('getClass', [], MethodGenerator::FLAG_PUBLIC, \sprintf('return \\%s::class;', $enumNamespace)))
                    ->setReturnType('string')
            );

            \file_put_contents($typeFile, FileGenerator::fromArray(['class' => $class])->generate());

            $doctrineTypes[$type.'_enum'] = [
                'class' => self::GENERATED_NAMESPACE.'\\'.$typeClass,
                'commented' => false,
            ];
        }

        if ([] !== $typeFiles) {
            $body = \array_map(function (string $file) {
                return \sprintf('require_once "%s";', $file);
            }, $typeFiles);

            $autoloadFile = $cacheDir.'/'.self::AUTOLOAD_FILE;
            \file_put_contents($autoloadFile, FileGenerator::fromArray([
                'body' => \implode(PHP_EOL, $body),
            ])->generate());

            \assert(\file_exists($autoloadFile));
            /** @noinspection PhpIncludeInspection */
            require_once $autoloadFile;
        }

        $container->setParameter(self::DOCTRINE_DBAL_CONNECTION_FACTORY_TYPES, $doctrineTypes);
    }
}
