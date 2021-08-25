<?php

declare(strict_types=1);

namespace App\Tests;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
use function str_starts_with;
use function substr;
use function ucfirst;

final class EasyAdminTest extends KernelTestCase
{
    /**
     * @psalm-suppress PossiblyNullArgument
     */
    public function test(): void
    {
        self::bootKernel();
        $configManager = self::getContainer()->get(ConfigManager::class);

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
                            static fn (string $method) => substr($method, 0, -6),
                            array_filter(
                                get_class_methods($config['controller']),
                                static fn (string $method) => str_ends_with($method, 'Action') && !str_starts_with($method, 'index'),
                            ),
                        ),
                    ),
                ),
                $config['disabled_actions'],
                [
                    'class',
                    'controller',
                    'disabled_actions',
                    'templates',
                    'name',
                    'label',
                    'form',
                    'translation_domain',
                    'primary_key_field_name',
                    'properties',
                ],
            );

            $requiredMethods = array_map(static fn (string $action) => 'test'.ucfirst($action), $actions);
            $existingMethods = get_class_methods($testClass);

            $missingTests = array_diff($requiredMethods, $existingMethods);

            self::assertEmpty($missingTests, sprintf('Required tests "%s" in %s', implode(', ', $missingTests), $testClassName));
        }
    }
}
