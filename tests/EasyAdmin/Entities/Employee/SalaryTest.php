<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Employee;

use App\Fixtures\Employee\EmployeeVasyaFixtures;
use App\Tests\EasyAdminTestCase;

final class SalaryTest extends EasyAdminTestCase
{
    /**
     * @see \App\Employee\Controller\SalaryController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'employee_id' => EmployeeVasyaFixtures::ID,
            'action' => 'new',
            'entity' => 'Salary',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
