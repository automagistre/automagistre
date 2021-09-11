<?php

declare(strict_types=1);

namespace App\Tests;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use ReflectionClass;
use ReflectionMethod;
use function array_diff;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function class_exists;
use function explode;
use function get_class_methods;
use function implode;
use function sprintf;
use function str_ends_with;
use function substr;
use function ucfirst;

final class EasyAdminTest extends KernelTestCase
{
    public function test(): void
    {
        self::bootKernel();
        $configManager = self::getContainer()->get(ConfigManager::class);

        $missingTests = [];

        foreach ($configManager->getBackendConfig('entities') as $entityName => $config) {
            $entityClass = $config['class'];
            $context = explode('\\', $entityClass)[1];
            $testClassName = $entityName.'Test';
            $testClass = "App\\Tests\\EasyAdmin\\Entities\\{$context}\\{$testClassName}";

            self::assertTrue(class_exists($testClass), sprintf('Test class %s not found, but expected.', $testClass));

            $actions = array_diff(
                array_unique(
                    array_merge(
                        array_keys($config),
                        array_map(
                            static fn (ReflectionMethod $method) => substr($method->name, 0, -6),
                            array_filter(
                                (new ReflectionClass($config['controller']))->getMethods(),
                                static fn (ReflectionMethod $method) => str_ends_with($method->name, 'Action'),
                            ),
                        ),
                    ),
                ),
                $config['disabled_actions'],
                [
                    'batch',
                    'class',
                    'controller',
                    'deleteBatch',
                    'disabled_actions',
                    'filters',
                    'form',
                    'index',
                    'label',
                    'name',
                    'primary_key_field_name',
                    'properties',
                    'templates',
                    'translation_domain',
                ],
            );

            $requiredMethods = array_map(static fn (string $action) => 'test'.ucfirst($action), $actions);
            $existingMethods = get_class_methods($testClass);

            foreach (array_diff($requiredMethods, $existingMethods) as $missingTestMethod) {
                $missingTests[] = sprintf('Missing tests %s::%s', $testClass, $missingTestMethod);
            }
        }

        if ([] !== $missingTests) {
            self::fail(implode(PHP_EOL, $missingTests));
        }
    }
}
