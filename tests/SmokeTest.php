<?php

declare(strict_types=1);

namespace App\Tests;

use App\Calendar\Fixtures\CalendarEntryFixtures;
use App\Car\Fixtures\Primera2004Fixtures;
use App\Car\Fixtures\RecommendationFixtures;
use App\Customer\Fixtures\OrganizationFixtures;
use App\Customer\Fixtures\PersonVasyaFixtures;
use App\Employee\Fixtures\EmployeeFixtures;
use App\Income\Fixtures\IncomeFixtures;
use App\Income\Fixtures\IncomePartFixtures;
use App\Manufacturer\Fixtures\NissanFixture;
use App\MC\Fixtures\EquipmentFixtures;
use App\MC\Fixtures\LineFixtures;
use App\MC\Fixtures\PartFixtures;
use App\MC\Fixtures\WorkFixtures;
use App\Order\Fixtures\OrderFixtures;
use App\Part\Fixtures\GasketFixture;
use App\Storage\Fixtures\MainWarehouseFixture;
use App\User\Fixtures\AdminFixtures;
use App\Vehicle\Fixtures\NissanGTRFixture;
use App\Wallet\Fixtures\WalletFixtures;
use function array_diff;
use function array_keys;
use function array_replace;
use function array_unique;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Generator;
use function http_build_query;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SmokeTest extends WebTestCase
{
    /**
     * @var string[][]
     */
    private const ADDITIONAL_QUERY = [
        'autocomplete' => ['query' => 'bla'],
        'search' => ['query' => 'bla'],
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
            'show' => ['id' => EmployeeFixtures::ID],
            'edit' => ['id' => EmployeeFixtures::ID],
            'salary' => ['operand_id' => PersonVasyaFixtures::ID],
            'penalty' => ['operand_id' => PersonVasyaFixtures::ID],
        ],
        'CustomerTransaction' => [
            'new' => ['operand_id' => PersonVasyaFixtures::ID, 'type' => 'increment'],
        ],
        'Salary' => [
            'new' => ['employee_id' => EmployeeFixtures::ID],
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
            'new' => ['part_id' => GasketFixture::ID],
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

        static::assertSame($statusCode, $response->getStatusCode());
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

        static::assertSame($statusCode, $response->getStatusCode());
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
                    self::ADDITIONAL_QUERY[$action] ?? [],
                    self::ADDITIONAL_QUERY[$entity][$action] ?? [],
                    ['action' => $action, 'entity' => $entity]
                );

                $isAjax = 'autocomplete' === $action;

                yield $entity.' '.$action => ['/?'.http_build_query($queries), 200, $isAjax];
            }
        }
    }
}
