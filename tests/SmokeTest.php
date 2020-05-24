<?php

declare(strict_types=1);

namespace App\Tests;

use App\Calendar\Fixtures\CalendarEntryFixtures;
use App\Income\Fixtures\IncomeFixtures;
use App\Part\Infrastructure\Fixtures\GasketFixture;
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
    private const ADDITIONAL_QUERY = [
        'edit' => ['id' => '1'],
        'autocomplete' => ['query' => 'bla'],
        'search' => ['query' => 'bla'],
        'CalendarEntry' => [
            'edit' => ['id' => CalendarEntryFixtures::ID],
        ],
        'OrderItemGroup' => [
            'new' => ['order_id' => '1'],
            'edit' => ['order_id' => '1', 'id' => '1'],
        ],
        'OrderItemService' => [
            'list' => ['car_id' => '1'],
            'search' => ['car_id' => '1'],
            'new' => ['order_id' => '1'],
            'edit' => ['order_id' => '1', 'id' => '2'],
            'autocomplete' => ['textOnly' => '1'],
        ],
        'OrderItemPart' => [
            'new' => ['order_id' => '1'],
            'edit' => ['order_id' => '1', 'id' => '3'],
        ],
        'OrderPrint' => [
            'matching' => ['id' => '1'],
            'giveOut' => ['id' => '1'],
            'finish' => ['id' => '1'],
            'act' => ['id' => '1'],
            'invoice' => ['id' => '1'],
        ],
        'CarRecommendation' => [
            'new' => ['car_id' => '1', 'order_id' => '1'],
            'edit' => ['order_id' => '1'],
        ],
        'CarRecommendationPart' => [
            'new' => ['recommendation_id' => '1'],
        ],
        'CarNote' => [
            'new' => ['car_id' => '1'],
        ],
        'OrganizationNote' => [
            'new' => ['organization_id' => '1'],
            'edit' => ['id' => '1'],
        ],
        'Person' => [
            'edit' => ['id' => '2'],
        ],
        'PersonNote' => [
            'new' => ['person_id' => '2'],
            'edit' => ['id' => '2'],
        ],
        'OrderNote' => [
            'new' => ['order_id' => '1'],
            'edit' => ['order_id' => '1'],
        ],
        'OperandTransaction' => [
            'new' => ['operand_id' => '1', 'type' => 'increment'],
        ],
        'MonthlySalary' => [
            'new' => ['employee_id' => '1'],
        ],
        'Part' => [
            'edit' => ['id' => GasketFixture::ID],
            'case' => ['part_id' => GasketFixture::ID],
        ],
        'Income' => [
            'edit' => ['id' => IncomeFixtures::ID],
        ],
        'IncomePart' => [
            'new' => ['income_id' => IncomeFixtures::ID],
        ],
        'McLine' => [
            'new' => ['mc_equipment_id' => '1'],
        ],
        'McPart' => [
            'new' => ['mc_line_id' => '1'],
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
     * @dataProvider admin
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

                yield $entity.' '.$action => ['/msk/?'.http_build_query($queries), 200, $isAjax];
            }
        }
    }

    public function admin(): Generator
    {
        yield ['/msk/report/profit', 200, false];
        yield ['/msk/report/part-sell', 200, false];
    }
}
