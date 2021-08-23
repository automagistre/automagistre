<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Customer;

use App\Tests\EasyAdminTestCase;

final class OperandTest extends EasyAdminTestCase
{
    /**
     * @see \App\Customer\Controller\CustomerController::showAction()
     */
    public function testAutocomplete(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'autocomplete',
            'entity' => 'Operand',
            'query' => 'Vas',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
