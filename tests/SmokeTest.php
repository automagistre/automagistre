<?php

declare(strict_types=1);

namespace App\Tests;

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
use function array_diff;
use function array_keys;
use function array_replace;
use function array_unique;
use function http_build_query;
use function in_array;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SmokeTest extends WebTestCase
{
    private const ADDITIONAL_QUERY = [
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
        'PartIncome' => [
            'income' => ['part_id' => GasketFixture::ID],
            'outcome' => ['part_id' => GasketFixture::ID],
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
     * @dataProvider anonymousPages
     */
    public function testAnonymous(string $url, int $statusCode): void
    {
        $client = self::createClient();
        $client->setServerParameter('HTTP_HOST', 'sto.automagistre.ru');

        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertSame($statusCode, $response->getStatusCode());
    }

    public function anonymousPages(): Generator
    {
        yield 'Login page' => ['/login', 200];
    }

    /**
     * @dataProvider easyadmin
     */
    public function testAuthenticated(string $url, int $statusCode, bool $ajax): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'employee@automagistre.ru',
            'PHP_AUTH_PW' => 'pa$$word',
        ]);
        $client->setServerParameter('HTTP_HOST', 'sto.automagistre.ru');

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
                    ['action' => $action, 'entity' => $entity]
                );

                $isAjax = 'autocomplete' === $action;

                yield $entity.' '.$action => ['/?'.http_build_query($queries), 200, $isAjax];
            }
        }
    }
}
