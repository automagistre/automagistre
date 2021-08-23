<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Employee;

use App\Fixtures\Customer\PersonVasyaFixtures;
use App\Fixtures\Employee\EmployeeVasyaFixtures;
use App\Tests\EasyAdminTestCase;

final class EmployeeTest extends EasyAdminTestCase
{
    /**
     * @see \App\Employee\Controller\EmployeeController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Employee',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Employee\Controller\EmployeeController::showAction()
     */
    public function testShow(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => EmployeeVasyaFixtures::ID,
            'action' => 'show',
            'entity' => 'Employee',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Employee\Controller\EmployeeController::newAction()
     */
    public function testNew(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'new',
            'entity' => 'Employee',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Employee\Controller\EmployeeController::editAction()
     */
    public function testEdit(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'id' => EmployeeVasyaFixtures::ID,
            'action' => 'edit',
            'entity' => 'Employee',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Employee\Controller\EmployeeController::salaryAction()
     */
    public function testSalary(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'operand_id' => PersonVasyaFixtures::ID,
            'action' => 'salary',
            'entity' => 'Employee',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Employee\Controller\EmployeeController::penaltyAction()
     */
    public function testPenalty(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'operand_id' => PersonVasyaFixtures::ID,
            'action' => 'penalty',
            'entity' => 'Employee',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Employee\Controller\EmployeeController::fireAction()
     */
    public function testFire(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'fire',
            'entity' => 'Employee',
            'id' => EmployeeVasyaFixtures::ID,
        ]));

        $response = $client->getResponse();

        self::assertTrue($response->isRedirect());
        self::assertSame('/msk/?action=list&entity=Employee', $response->headers->get('Location'));
    }
}
