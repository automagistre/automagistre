<?php

declare(strict_types=1);

namespace App\Tests;

use App\Fixtures\Appeal\AppealFixtures;
use App\Fixtures\Calendar\CalendarEntryFixtures;
use App\Fixtures\Car\Primera2004Fixtures;
use App\Fixtures\Car\RecommendationFixtures;
use App\Fixtures\Customer\OrganizationFixtures;
use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Fixtures\Employee\AdminFixtures;
use App\Fixtures\Employee\EmployeeVasyaFixtures;
use App\Fixtures\Income\IncomeFixtures;
use App\Fixtures\Income\IncomePartFixtures;
use App\Fixtures\Manufacturer\NissanFixture;
use App\Fixtures\Mc\EquipmentFixtures;
use App\Fixtures\Mc\LineFixtures;
use App\Fixtures\Mc\PartFixtures;
use App\Fixtures\Mc\WorkFixtures;
use App\Fixtures\Order\OrderFixtures;
use App\Fixtures\Part\GasketFixture;
use App\Fixtures\Storage\MainWarehouseFixture;
use App\Fixtures\Vehicle\NissanGTRFixture;
use App\Fixtures\Wallet\WalletFixtures;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use function array_diff;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_replace;
use function array_unique;
use function explode;
use function get_class_methods;
use function http_build_query;
use function in_array;
use function sprintf;
use function str_ends_with;
use function substr;
use function var_export;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SmokeTest extends WebTestCase
{
    private const ADDITIONAL_QUERY = [
        'Appeal' => [
            'show' => ['id' => AppealFixtures::ID],
            'status' => ['id' => AppealFixtures::ID],
        ],
        'CalendarEntry' => [
            'edit' => ['id' => CalendarEntryFixtures::ID],
        ],
        'CarModel' => [
            'show' => ['id' => NissanGTRFixture::ID],
            'edit' => ['id' => NissanGTRFixture::ID],
        ],
        'Order' => [
            'show' => ['id' => OrderFixtures::ID],
        ],
        'OrderItemGroup' => [
            'new' => ['order_id' => OrderFixtures::ID],
            'edit' => ['id' => OrderFixtures::GROUP_ID],
        ],
        'OrderItemService' => [
            'list' => ['car_id' => Primera2004Fixtures::ID],
            'search' => ['car_id' => Primera2004Fixtures::ID],
            'new' => ['order_id' => OrderFixtures::ID],
            'edit' => ['id' => OrderFixtures::SERVICE_ID],
            'autocomplete' => ['textOnly' => '1'],
        ],
        'OrderItemPart' => [
            'new' => ['order_id' => OrderFixtures::ID],
            'edit' => ['id' => OrderFixtures::PART_ID],
        ],
        'OrderPrint' => [
            'matching' => ['id' => OrderFixtures::ID],
            'giveOut' => ['id' => OrderFixtures::ID],
            'finish' => ['id' => OrderFixtures::ID],
            'upd' => ['id' => OrderFixtures::ID],
            'invoice' => ['id' => OrderFixtures::ID],
        ],
        'OrderProfit' => [
        ],
        'Car' => [
            'show' => ['id' => Primera2004Fixtures::ID],
            'edit' => ['id' => Primera2004Fixtures::ID],
        ],
        'CarRecommendation' => [
            'new' => ['car_id' => Primera2004Fixtures::ID, 'order_id' => OrderFixtures::ID],
            'edit' => ['id' => RecommendationFixtures::ID, 'order_id' => OrderFixtures::ID],
        ],
        'CarRecommendationPart' => [
            'new' => ['recommendation_id' => RecommendationFixtures::ID],
            'edit' => ['id' => RecommendationFixtures::RECOMMENDATION_PART_ID],
        ],
        'Person' => [
            'show' => ['id' => PersonVasyaFixtures::ID],
            'edit' => ['id' => PersonVasyaFixtures::ID],
        ],
        'Organization' => [
            'show' => ['id' => OrganizationFixtures::ID],
            'edit' => ['id' => OrganizationFixtures::ID],
        ],
        'Employee' => [
            'show' => ['id' => EmployeeVasyaFixtures::ID],
            'edit' => ['id' => EmployeeVasyaFixtures::ID],
            'salary' => ['operand_id' => PersonVasyaFixtures::ID],
            'penalty' => ['operand_id' => PersonVasyaFixtures::ID],
        ],
        'CustomerTransaction' => [
            'new' => ['operand_id' => PersonVasyaFixtures::ID, 'type' => 'increment'],
        ],
        'Salary' => [
            'new' => ['employee_id' => EmployeeVasyaFixtures::ID],
        ],
        'Part' => [
            'edit' => ['id' => GasketFixture::ID],
        ],
        'PartCase' => [
            'case' => ['part_id' => GasketFixture::ID],
        ],
        'Motion' => [
            'increase' => ['part_id' => GasketFixture::ID],
            'decrease' => ['part_id' => GasketFixture::ID],
            'actualize' => ['part_id' => GasketFixture::ID],
        ],
        'PartCross' => [
            'cross' => ['part_id' => GasketFixture::ID],
            //            'uncross' => ['part_id' => GasketFixture::ID], // TODO GET request must not mutate state
        ],
        'PartPrice' => [
            'new' => ['part_id' => GasketFixture::ID],
        ],
        'PartDiscount' => [
            'new' => ['part_id' => GasketFixture::ID],
        ],
        'PartRequiredAvailability' => [
            'new' => ['part_id' => GasketFixture::ID],
        ],
        'PartSupply' => [
            'increase' => ['part_id' => GasketFixture::ID],
            'decrease' => ['part_id' => GasketFixture::ID, 'supplier_id' => NissanFixture::ID],
        ],
        'PartSell' => [
        ],
        'Income' => [
            'edit' => ['id' => IncomeFixtures::ID],
        ],
        'IncomePart' => [
            'new' => ['income_id' => IncomeFixtures::ID],
            'edit' => ['id' => IncomePartFixtures::ID],
        ],
        'Manufacturer' => [
            'edit' => ['id' => NissanFixture::ID],
        ],
        'McLine' => [
            'new' => ['mc_equipment_id' => EquipmentFixtures::ID],
            'edit' => ['id' => LineFixtures::ID],
        ],
        'McWork' => [
            'edit' => ['id' => WorkFixtures::ID],
        ],
        'Note' => [
            'new' => ['subject' => PersonVasyaFixtures::ID],
        ],
        'McPart' => [
            'new' => ['mc_line_id' => LineFixtures::ID],
            'edit' => ['id' => PartFixtures::ID],
        ],
        'User' => [
            'show' => ['id' => AdminFixtures::ID],
            'edit' => ['id' => AdminFixtures::ID],
        ],
        'Wallet' => [
            'edit' => ['id' => WalletFixtures::ID],
        ],
        'Warehouse' => [
            'edit' => ['id' => MainWarehouseFixture::ID],
        ],
    ];

    /**
     * @dataProvider easyadmin
     */
    public function testAuthenticated(string $url, int $statusCode, bool $ajax): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'employee@automagistre.ru',
            'PHP_AUTH_PW' => 'pa$$word',
        ]);

        if ($ajax) {
            $client->xmlHttpRequest('GET', $url);
        } else {
            $client->request('GET', $url);
        }
        $response = $client->getResponse();

        self::assertSame($statusCode, $response->getStatusCode());
    }

    public function easyadmin(): Generator
    {
        $kernel = self::bootKernel();
        /** @var ConfigManager $configManager */
        $configManager = $kernel->getContainer()->get('test.service_container')->get(ConfigManager::class);
        self::ensureKernelShutdown();

        foreach ($configManager->getBackendConfig('entities') as $entity => $config) {
            $actions = array_diff(['list', 'new', 'edit', 'autocomplete', 'search'], $config['disabled_actions']);
            $actions = [...$actions, ...(array_keys(self::ADDITIONAL_QUERY[$entity] ?? []))];
            $actions = array_unique($actions);

            foreach ($actions as $action) {
                $queries = array_replace(
                    in_array($action, ['search', 'autocomplete'], true) ? ['query' => 'bla'] : [],
                    self::ADDITIONAL_QUERY[$entity][$action] ?? [],
                    ['action' => $action, 'entity' => $entity],
                );

                $isAjax = 'autocomplete' === $action;

                yield $entity.' '.$action => ['/msk/?'.http_build_query($queries), 200, $isAjax];
            }
        }
    }

    public function test(): void
    {
        self::bootKernel();
        $configManager = self::getContainer()->get(ConfigManager::class);
        $filesystem = new Filesystem();

        foreach ($configManager->getBackendConfig('entities') as $entity => $config) {
            $controllerClass = $config['controller'];

            $actions = array_diff(
                array_unique(
                    array_merge(
                        array_keys($config),
                        array_keys(self::ADDITIONAL_QUERY[$entity] ?? []),
                        array_filter(
                            array_map(
                                static fn (string $method) => str_ends_with($method, 'Action')
                                    ? substr($method, 0, -6, )
                                    : null,
                                get_class_methods($controllerClass),
                            ),
                            static fn (?string $action) => !in_array($action, [null, 'index'], true),
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

            $context = explode('\\', $config['class'])[1];
            $testClass = $config['name'].'Test';
            $testClassDir = sprintf('%s/EasyAdmin/Entities/%s', __DIR__, $context);
            $testClassPath = sprintf('%s/%s.php', $testClassDir, $testClass);

            $filesystem->mkdir($testClassDir);

            $methods = [];
            foreach ($actions as $action) {
                $methodName = ucfirst($action);

                $query =
                    array_replace(
                        in_array($action, ['search', 'autocomplete'], true) ? ['query' => 'bla'] : [],
                        self::ADDITIONAL_QUERY[$entity][$action] ?? [],
                        ['action' => $action, 'entity' => $entity],
                    );

                $fixtures = [
                    AppealFixtures::ID => 'AppealFixtures::ID',
                    CalendarEntryFixtures::ID => 'CalendarEntryFixtures::ID',
                    NissanGTRFixture::ID => 'NissanGTRFixture::ID',
                    OrderFixtures::ID => 'OrderFixtures::ID',
                    OrderFixtures::GROUP_ID => 'OrderFixtures::GROUP_ID',
                    Primera2004Fixtures::ID => 'Primera2004Fixtures::ID',
                    OrderFixtures::SERVICE_ID => 'OrderFixtures::SERVICE_ID',
                    OrderFixtures::PART_ID => 'OrderFixtures::PART_ID',
                    RecommendationFixtures::ID => 'RecommendationFixtures::ID',
                    RecommendationFixtures::RECOMMENDATION_PART_ID => 'RecommendationFixtures::RECOMMENDATION_PART_ID',
                    PersonVasyaFixtures::ID => 'PersonVasyaFixtures::ID',
                    OrganizationFixtures::ID => 'OrganizationFixtures::ID',
                    EmployeeVasyaFixtures::ID => 'EmployeeVasyaFixtures::ID',
                    GasketFixture::ID => 'GasketFixture::ID',
                    NissanFixture::ID => 'NissanFixture::ID',
                    IncomeFixtures::ID => 'IncomeFixtures::ID',
                    IncomePartFixtures::ID => 'IncomePartFixtures::ID',
                    EquipmentFixtures::ID => 'EquipmentFixtures::ID',
                    LineFixtures::ID => 'LineFixtures::ID',
                    WorkFixtures::ID => 'WorkFixtures::ID',
                    PartFixtures::ID => 'PartFixtures::ID',
                    AdminFixtures::ID => 'AdminFixtures::ID',
                    WalletFixtures::ID => 'WalletFixtures::ID',
                    MainWarehouseFixture::ID => 'MainWarehouseFixture::ID',
                ];
                $query = array_map(
                    static fn (string $value) => array_key_exists($value, $fixtures) ? $fixtures[$value] : $value,
                    $query,
                );

                $query = var_export($query, true);

                $query = \Safe\preg_replace('/\'([a-zA-Z0-9]+::[A-Z_]+)\'/', ' $1', $query);

                $methods[] = <<<PHP
                    /**
                     * @see \\{$controllerClass}::{$action}Action()
                     */
                    public function test{$methodName}(): void {
                        \$client = self::createClient();

                        \$client->request('GET', '/msk/?'.http_build_query({$query}));

                        \$response = \$client->getResponse();

                        self::assertSame(200, \$response->getStatusCode());
                    }
                    PHP;
            }

            if ([] === $actions) {
                $methods[] = <<<'PHP'
                public function test(): void {
                    self::markTestSkipped('Not implemented yet.');
                }
                PHP;
            }
            $methods = implode(PHP_EOL.PHP_EOL, $methods);
            $filesystem->dumpFile(
                $testClassPath,
                <<<PHP
            <?php

            declare(strict_types=1);

            namespace App\\Tests\\EasyAdmin\\Entities\\{$context};

            use App\\Tests\\EasyAdminTestCase;
            use App\\Fixtures\\Appeal\\AppealFixtures;
            use App\\Fixtures\\Calendar\\CalendarEntryFixtures;
            use App\\Fixtures\\Car\\Primera2004Fixtures;
            use App\\Fixtures\\Car\\RecommendationFixtures;
            use App\\Fixtures\\Customer\\OrganizationFixtures;
            use App\\Fixtures\\Customer\\PersonVasyaFixtures;
            use App\\Fixtures\\Employee\\AdminFixtures;
            use App\\Fixtures\\Employee\\EmployeeVasyaFixtures;
            use App\\Fixtures\\Income\\IncomeFixtures;
            use App\\Fixtures\\Income\\IncomePartFixtures;
            use App\\Fixtures\\Manufacturer\\NissanFixture;
            use App\\Fixtures\\Mc\\EquipmentFixtures;
            use App\\Fixtures\\Mc\\LineFixtures;
            use App\\Fixtures\\Mc\\PartFixtures;
            use App\\Fixtures\\Mc\\WorkFixtures;
            use App\\Fixtures\\Order\\OrderFixtures;
            use App\\Fixtures\\Part\\GasketFixture;
            use App\\Fixtures\\Storage\\MainWarehouseFixture;
            use App\\Fixtures\\Vehicle\\NissanGTRFixture;
            use App\\Fixtures\\Wallet\\WalletFixtures;

            final class {$testClass} extends EasyAdminTestCase
            {
                {$methods}
            }

            PHP,
            );
        }
    }
}
